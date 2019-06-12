<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DashboardController.
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }
}