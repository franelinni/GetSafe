<?php

namespace App\Repository;

use App\Entity\InputFileImageRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InputFileImageRelation>
 *
 * @method InputFileImageRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputFileImageRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputFileImageRelation[]    findAll()
 * @method InputFileImageRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputFileImageRelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputFileImageRelation::class);
    }

//    /**
//     * @return InputFileImageRelation[] Returns an array of InputFileImageRelation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InputFileImageRelation
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
