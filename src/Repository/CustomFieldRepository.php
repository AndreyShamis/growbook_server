<?php

namespace App\Repository;

use App\Entity\CustomField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustomField|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomField|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomField[]    findAll()
 * @method CustomField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFieldRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomField::class);
    }

    /**
     * @param array $c
     * @param bool $persist
     * @return CustomField
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findOrCreate(array $c, bool $persist=false): CustomField
    {
        $ret = $this->findOneBy(array(
            'object_host_id' => $c['obj']->getId(),
            'object_host_type' => get_class($c['obj']),
            'property' => $c['key'],
        ));
        if ($ret === null) {
            $ret = new CustomField();
            $ret->setObjectHostId($c['obj']->getId());
            $ret->setObjectHostType(get_class($c['obj']));
            $ret->setProperty($c['key']);
            if ($persist) {
                $this->_em->persist($ret);
                $this->_em->flush($ret);
            }
        }
        return $ret;
    }


    /**
     * @param object $obj
     * @param string $key
     * @return CustomField
     */
    public function findForObject($obj, string $key): CustomField
    {
        $ret = $this->findOneBy(array(
            'object_host_id' => $obj->getId(),
            'object_host_type' => get_class($obj),
            'property' => $key,
        ));
        if ($ret === null) {
            $ret = new CustomField();
        }
        return $ret;
    }

    /**
     * @param object $obj
     * @return CustomField[]
     */
    public function findAllForObject($obj): array
    {
        $ret = $this->findBy(array(
            'object_host_id' => $obj->getId(),
            'object_host_type' => get_class($obj)
        ));
        return $ret;
    }

    // /**
    //  * @return CustomField[] Returns an array of CustomField objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CustomField
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
