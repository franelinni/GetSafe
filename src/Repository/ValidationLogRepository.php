<?php

namespace App\Repository;

use App\Entity\ValidationLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValidationLog>
 *
 * @method ValidationLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValidationLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValidationLog[]    findAll()
 * @method ValidationLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValidationLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValidationLog::class);
    }

//    /**
//     * @return ValidationLog[] Returns an array of ValidationLog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ValidationLog
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
