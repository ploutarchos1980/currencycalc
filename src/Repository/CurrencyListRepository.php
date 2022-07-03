<?php

namespace App\Repository;

use App\Entity\CurrencyList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyList>
 *
 * @method CurrencyList|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyList|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyList[]    findAll()
 * @method CurrencyList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyList::class);
    }

    public function add(CurrencyList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CurrencyList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCoins($fromCoin, $toCoin): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT * FROM currency_list c
        WHERE (c.from_coin = :fromCoin AND c.to_coin = :toCoin) 
        LIMIT 0, 1
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('fromCoin', $fromCoin);
        $stmt->bindParam('toCoin', $toCoin);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }
}
