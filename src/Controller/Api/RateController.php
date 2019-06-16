<?php

namespace App\Controller\Api;

use App\Entity\CurrencyPair;
use App\Entity\ExchangeRate;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class RateController.
 *
 * @Route("/api/rate")
 */
class RateController extends AbstractController
{
    /**
     * @Route("/history/{id}", name="get_rate_history", methods={"GET"})
     * @param CurrencyPair $currencyPair
     *
     * @return JsonResponse
     */
    public function historyAction(CurrencyPair $currencyPair)
    {
        $em = $this->getDoctrine()->getManager();
        $ratesData = $em->getRepository(ExchangeRate::class)->findBy(['currencyPair' => $currencyPair], ['date' => Criteria::ASC]);
        
        return $this->json($ratesData, 200);
    }
}