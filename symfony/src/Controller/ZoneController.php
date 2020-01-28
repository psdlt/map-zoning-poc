<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Repository\PricingRepository;
use App\Repository\ZoneRepository;
use App\Transformer\OsmTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/zone")
 */
class ZoneController extends AbstractController
{
    private $transformer;
    private $zones;
    private $pricing;

    public function __construct(
        OsmTransformer $transformer,
        ZoneRepository $zones,
        PricingRepository $pricing
    ) {
        $this->transformer = $transformer;
        $this->zones = $zones;
        $this->pricing = $pricing;
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request)
    {
        $data = json_decode($request->getContent());
        $transformed = $this->transformer->toZone($data);

        // persist
        $em = $this->getDoctrine()->getManager();

        foreach ($transformed->getPolygons() as $polygon) {
            $em->persist($polygon);
        }

        $em->persist($transformed);
        $em->flush();


        return new Response('', 204);
    }

    /**
     * @Route("")
     */
    public function all()
    {
        $zones = [];
        foreach ($this->zones->findAll() as $zone) {
            $zones[] = $this->transformer->toModel($zone);
        }
        return $this->json($zones);
    }

    /**
     * @Route("/detailed/{uuid}")
     */
    public function detailed(Zone $zone)
    {
        return $this->json($this->transformer->toDetailedModel($zone));
    }

    /**
     * @param Zone $zone
     * @Route("/polygons/{uuid}", methods={"POST"})
     * @return Response
     */
    public function updatePolys(Request $request, Zone $zone)
    {
        $data = json_decode($request->getContent());
        $polys = [];
        foreach ($data as $poly) {
            $polys = array_merge($polys, $this->transformer->arrayToPolygon($poly));
        }

        $em = $this->getDoctrine()->getManager();

        foreach ($zone->getPolygons() as $polygon) {
            $em->remove($polygon);
        }

        foreach ($polys as $poly) {
            $em->persist($poly);
            $zone->addPolygon($poly);
        }

        $em->persist($zone);
        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }


    /**
     * @param Zone $zone
     * @return Response
     * @Route("/delete/{uuid}", methods={"DELETE"})
     */
    public function delete(Zone $zone)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($zone->getPolygons() as $polygon) {
            $em->remove($polygon);
        }
        foreach ($this->pricing->getRelatedPricing($zone) as $pricing) {
            $em->remove($pricing);
        }
        $em->remove($zone);
        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
