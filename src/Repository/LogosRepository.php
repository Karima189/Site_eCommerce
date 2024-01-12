<?php

namespace App\Repository;

use App\Entity\Logos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Logos>
 *
 * @method Logos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logos[]    findAll()
 * @method Logos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logos::class);
    }

//    /**
//     * @return Logos[] Returns an array of Logos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Logos
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
