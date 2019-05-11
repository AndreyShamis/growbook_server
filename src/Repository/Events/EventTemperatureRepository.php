<?php

namespace App\Repository\Events;

use App\Entity\Events\EventTemperature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventTemperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventTemperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventTemperature[]    findAll()
 * @method EventTemperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTemperatureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventTemperature::class);
    }

    // /**
    //  * @return EventTemperature[] Returns an array of EventTemperature objects
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
    public function findOneBySomeField($value): ?EventTemperature
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
