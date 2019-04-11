<?php

namespace App\Repository;

use App\Entity\Bacon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Bacon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bacon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bacon[]    findAll()
 * @method Bacon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaconRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bacon::class);
    }

    // /**
    //  * @return Bacon[] Returns an array of Bacon objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bacon
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
