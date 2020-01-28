<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Service\PricingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pricing")
 */
class PricingController extends AbstractController
{
    public $pricing;

    public function __construct(
        PricingService $pricing
    ) {
        $this->pricing = $pricing;
    }

    /**
     * @Route("", methods={"POST"})
     */
    public function setPricing(Request $request)
    {
        $data = json_decode($request->getContent());
        $this->pricing->setPricing($data->fromZone, $data->toZone, $data->price);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/zone/{zone}", methods={"GET"})
     * @param Zone $zone
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getPricing(Zone $zone)
    {
        return $this->json($this->pricing->getPricingsFrom($zone));
    }
}
