<?php

namespace App\Repository;

use App\Entity\CarouselFront;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarouselFront>
 *
 * @method CarouselFront|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarouselFront|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarouselFront[]    findAll()
 * @method CarouselFront[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselFrontRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarouselFront::class);
    }

//    /**
//     * @return CarouselFront[] Returns an array of CarouselFront objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CarouselFront
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
