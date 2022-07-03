<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\CurrencyList;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\Request;

use App\Resources\OutputResponse;

class DashboardController extends AbstractController
{
    
    public function index(): Response
    {
        return $this->render('index/dashboard.html.twig');
    }

    public function getcoins(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {

        $coinsOutput = $doctrine->getRepository(CurrencyList::class)->findAll();

        if (!$coinsOutput) {
            return new JsonResponse([]);
        }

        return new Response(
            $serializer->serialize($coinsOutput, JsonEncoder::FORMAT),
            200,
            array_merge([], ['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

 

    public function action(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer, string $todo): Response
    {
        $output = OutputResponse::ERROR;

        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['STATUS' => 'ERROR']);
            exit;
        }
        
        if ($todo === 'delete') {
            $output = $this->deleteRecord($doctrine, $serializer, $data);
        }
        else if ($todo === 'create'){
            $output = $this->createRecord($doctrine, $serializer, $data);
        }
        else if ($todo === 'update') {
            $output = $this->updateRecord($doctrine, $serializer, $data);
        }

        $status = '';

        if ($output === OutputResponse::DONE) {
            $status = 'DONE';
        }
        elseif ($output === OutputResponse::EXIST) {
            $status = 'EXIST';
        }
        else {
            $status = 'ERROR';
        }
       
        return new JsonResponse([
            'STATUS' => $status
        ]); 
    }
    

    public function createRecord (ManagerRegistry $doctrine, SerializerInterface $serializer, $data) 
    {
        $output = OutputResponse::ERROR;

        try 
        {
            $entityManager = $doctrine->getManager();

            if (isset($data['fromCoin']) && isset($data['toCoin']) && isset($data['rate'])) {

                $checkExist = $doctrine->getRepository(CurrencyList::class)->findCoins($data['fromCoin'], $data['toCoin']);

                if (!$checkExist) {
                    $coin = new CurrencyList();
                    $coin->setFromCoin($data['fromCoin']);
                    $coin->setToCoin($data['toCoin']);
                    $coin->setRate(floatval($data['rate']));
    
                    $entityManager->persist($coin);
                    $entityManager->flush();
                    $output = OutputResponse::DONE;
                }
                else
                {
                    $output = OutputResponse::EXIST;
                }
            }
        } 
        catch(Exception $e) {
            $output = OutputResponse::ERROR;    
        }

        return $output;
    } 

    public function deleteRecord (ManagerRegistry $doctrine, SerializerInterface $serializer, $data) 
    {
        $output = OutputResponse::ERROR;

        try 
        {
            if (isset($data['coinid'])) {
                $entityManager = $doctrine->getManager();
                $coins = $doctrine->getRepository(CurrencyList::class)->find($data['coinid']);
                if ($coins) {
                    $entityManager->remove($coins);
                    $entityManager->flush();
                    $output = OutputResponse::DONE;
                }
            }
        } 
        catch(Exception $e) {
            $output = OutputResponse::ERROR;    
        }

        return $output;
    } 

    public function updateRecord (ManagerRegistry $doctrine, SerializerInterface $serializer, $data) 
    {
        $output = OutputResponse::ERROR;

        try 
        {
            if (isset($data['coinid'])) {
                $entityManager = $doctrine->getManager();
                $coins = $doctrine->getRepository(CurrencyList::class)->find($data['coinid']);
                
                if ($coins) {
                    $coins->setFromCoin($data['fromCoin']);
                    $coins->setToCoin($data['toCoin']);
                    $coins->setRate(floatval($data['rate']));
                    $entityManager->flush();
                    $output = OutputResponse::DONE;
                }
            }
        } 
        catch(Exception $e) {
            $output = OutputResponse::ERROR;    
        }

        return $output;
    } 

}
