<?php

namespace App\Repository;

use App\Entity\EventsEventHumidity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventsEventHumidity|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventsEventHumidity|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventsEventHumidity[]    findAll()
 * @method EventsEventHumidity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventsEventHumidityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventsEventHumidity::class);
    }

    // /**
    //  * @return EventsEventHumidity[] Returns an array of EventsEventHumidity objects
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
    public function findOneBySomeField($value): ?EventsEventHumidity
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
