<?php

namespace App\Repository\Events;

use App\Entity\Events\EventHumidity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventHumidity|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventHumidity|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventHumidity[]    findAll()
 * @method EventHumidity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventHumidityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventHumidity::class);
    }

    // /**
    //  * @return EventHumidity[] Returns an array of EventHumidity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventHumidity
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
