<?php

namespace App\Repository;

use App\Entity\FeedFertilizer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FeedFertilizer|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedFertilizer|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedFertilizer[]    findAll()
 * @method FeedFertilizer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedFertilizerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FeedFertilizer::class);
    }

    /**
     * @param array $criteria
     * @return FeedFertilizer|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findOrCreate(array $criteria)
    {
        $entity = $this->findOneBy([
            'fertilizer' => $criteria['fertilizer'],
            'event' => $criteria['event']
            ]);
        if ($entity === null) {
            $entity = new FeedFertilizer();
            $entity->setFertilizer($criteria['fertilizer']);
            $entity->setEvent($criteria['event']);
            $entity->setAmount(0);
            $this->_em->persist($entity);
            $this->_em->flush();
        }
        return $entity;
    }

    // /**
    //  * @return FeedFertilizer[] Returns an array of FeedFertilizer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FeedFertilizer
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
