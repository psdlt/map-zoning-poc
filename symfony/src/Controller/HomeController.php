<?php

namespace App\Controller;

use App\Form\SearchShipmentType;
use App\Model\SearchShipment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function home()
    {
        $form = $this->createForm(SearchShipmentType::class, new SearchShipment());

        return $this->render('base.html.twig', [
            'shipmentForm' => $form->createView(),
        ]);
    }
}
