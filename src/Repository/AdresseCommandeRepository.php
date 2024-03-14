<?php

namespace App\Repository;

use App\Entity\AdresseCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdresseCommande>
 *
 * @method AdresseCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdresseCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdresseCommande[]    findAll()
 * @method AdresseCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdresseCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdresseCommande::class);
    }

//    /**
//     * @return AdresseCommande[] Returns an array of AdresseCommande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AdresseCommande
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
