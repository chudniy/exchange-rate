<?php

namespace App\Controller;

use App\Entity\CurrencyPair;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DashboardController.
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $currencyPairs = $em->getRepository(CurrencyPair::class)->findAll();
        
        return [
            'currencyPairs' => $currencyPairs,
        ];
    }
}