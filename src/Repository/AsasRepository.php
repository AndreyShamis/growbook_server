<?php

namespace App\Repository;

use App\Entity\Asas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Asas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asas[]    findAll()
 * @method Asas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AsasRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Asas::class);
    }

    // /**
    //  * @return Asas[] Returns an array of Asas objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Asas
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
