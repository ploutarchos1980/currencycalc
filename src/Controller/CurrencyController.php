<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\CurrencyList;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

use Symfony\Component\Serializer\SerializerInterface;

use App\Resources\OutputResponse;

class CurrencyController extends AbstractController
{
    public function index(ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
    {
        $coins = $doctrine->getRepository(CurrencyList::class)->findAll();

        if (!$coins) {
            return new JsonResponse([]);
        }

        $coinsOutput = [];

        foreach ($coins as $coin) {
            if (!in_array($coin->getToCoin(), $coinsOutput)) {
                $coinsOutput[]  = $coin->getToCoin();
            }
            if (!in_array($coin->getFromCoin(), $coinsOutput)) {
                $coinsOutput[]  = $coin->getFromCoin();
            }
        }

        return new JsonResponse(
            $coinsOutput
        );
    }
   
    public function convert(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
    {
        $output = OutputResponse::ERROR;
        $_convertedValue = 0.000;

        $_from = $request->query->get('from');
        $_to = $request->query->get('to');

        if (!$_from || !$_to) {
            return new JsonResponse(['STATUS' => 'ERROR']);
            exit;
        }

        $caseFound = $doctrine->getRepository(CurrencyList::class)->findCoins($_from, $_to);
        
        if (!$caseFound) {
            $caseAlternative = $doctrine->getRepository(CurrencyList::class)->findCoins($_to, $_from);
            if ($caseAlternative)
            {
                $_convertedValue = 1 / floatval($caseAlternative[0]['rate']);
                $output = OutputResponse::DONE;
            }
        }
        else
        {
            $_convertedValue = floatval($caseFound[0]['rate']);
            $output = OutputResponse::DONE;
        }

        return new JsonResponse([
            'CONVERTED_VALUE' => $_convertedValue, 'STATUS' => (($output === OutputResponse::ERROR) ? 'ERROR' : 'DONE' )
        ]); 
    }
    

    // This controller builed only to add the first records to database
    public function addCoins(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        $coin = new CurrencyList();
        $coin->setFromCoin('Euro');
        $coin->setToCoin('US Dollar');
        $coin->setRate(1.3764);

        $coin2 = new CurrencyList();
        $coin2->setFromCoin('Euro');
        $coin2->setToCoin('Swiss Franc');
        $coin2->setRate(1.2079);

        $coin3 = new CurrencyList();
        $coin3->setFromCoin('Euro');
        $coin3->setToCoin('British Pound');
        $coin3->setRate(0.8731);

        $coin4 = new CurrencyList();
        $coin4->setFromCoin('US Dollar');
        $coin4->setToCoin('JPY');
        $coin4->setRate(76.7200);

        $coin5 = new CurrencyList();
        $coin5->setFromCoin('Swiss Franc');
        $coin5->setToCoin('US Dollar');
        $coin5->setRate(1.1379);

        $coin6 = new CurrencyList();
        $coin6->setFromCoin('British Pound');
        $coin6->setToCoin('CAD');
        $coin6->setRate(1.5648);


        $entityManager->persist($coin);
        $entityManager->persist($coin2);
        $entityManager->persist($coin3);
        $entityManager->persist($coin4);
        $entityManager->persist($coin5);
        $entityManager->persist($coin6);

        $entityManager->flush();

        return new Response('Saved...');
    }
}
