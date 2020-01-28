<?php

namespace App\Controller;

use App\Entity\Polygon;
use App\Entity\Zone;
use App\Form\SearchShipmentType;
use App\Model\SearchShipment;
use App\Repository\PolygonRepository;
use App\Repository\PricingRepository;
use App\Service\LocationService;
use App\Service\TurfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shipment")
 */
class ShipmentController extends AbstractController
{
    private $location;
    private $polygons;
    private $pricing;
    private $turf;

    public function __construct(
        LocationService $location,
        PolygonRepository $polygons,
        PricingRepository $pricing,
        TurfService $turf
    ) {
        $this->location = $location;
        $this->polygons = $polygons;
        $this->pricing = $pricing;
        $this->turf = $turf;
    }

    /**
     * @Route("/ship", methods={"POST"})
     */
    public function ship(Request $request)
    {
        $form = $this->createForm(SearchShipmentType::class, new SearchShipment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SearchShipment $search */
            $search = $form->getData();

            $fromPoint = $this->location->getPoint($search->from);
            $toPoint = $this->location->getPoint($search->to);

            // "from" zone
            $fromZone = null;

            /** @var Zone[] $zones */
            $fromZones = array_map(function (Polygon $polygon) {
                return $polygon->getZone();
            }, $this->turf->weDontNeedNoHoles($this->polygons->findMatchingZones($fromPoint)));
            if (count($fromZones) > 0) {
                usort($fromZones, function (Zone $left, Zone $right) {
                    if ($left->getArea() === $right->getArea()) {
                        return 0;
                    }

                    return $left->getArea() > $right->getArea() ? 1 : -1;
                });
                $fromZone = $fromZones[0];
            }

            // "to" zone
            $toZone = null;

            /** @var Zone[] $zones */
            $toZones = array_map(function (Polygon $polygon) {
                return $polygon->getZone();
            }, $this->turf->weDontNeedNoHoles($this->polygons->findMatchingZones($toPoint)));
            if (count($toZones) > 0) {
                usort($toZones, function (Zone $left, Zone $right) {
                    if ($left->getArea() === $right->getArea()) {
                        return 0;
                    }

                    return $left->getArea() > $right->getArea() ? 1 : -1;
                });
                $toZone = $toZones[0];
            }

            // if we have both "from" and "to" zones - lookup pricing
            $price = null;
            if (count($fromZones) > 0 && count($toZones) > 0) {
                // zones are already sorted from "most specific" to "least specific"
                foreach ($fromZones as $candidateFromZone) {
                    foreach ($toZones as $candidateToZone) {
                        $pricing = $this->pricing->findOneBy([
                            'fromZone' => $candidateFromZone,
                            'toZone' => $candidateToZone,
                        ]);
                        if ($pricing) {
                            $price = $pricing->getPrice();
                            // change selected zones
                            $fromZone = $candidateFromZone;
                            $toZone = $candidateToZone;
                            break 2;
                        }
                    }
                }
            }

            return $this->json([
                'from' => $fromPoint,
                'to' => $toPoint,
                'fromZone' => $fromZone ? $fromZone->getUuid() : null,
                'toZone' => $toZone ? $toZone->getUuid() : null,
                'price' => $price,
            ]);
        }

        return $this->json(['error' => true], Response::HTTP_BAD_REQUEST);
    }
}
