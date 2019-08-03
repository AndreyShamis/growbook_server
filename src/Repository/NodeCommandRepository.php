<?php

namespace App\Repository;

use App\Entity\NodeCommand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NodeCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method NodeCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method NodeCommand[]    findAll()
 * @method NodeCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NodeCommandRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NodeCommand::class);
    }

    // /**
    //  * @return NodeCommand[] Returns an array of NodeCommand objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NodeCommand
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
