<?php

namespace BiberLtd\Bundle\ShipmentGatewayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BiberLtdShipmentGatewayBundle:Default:index.html.twig', array('name' => $name));
    }
}
