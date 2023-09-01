<?php

namespace App\Repository;

use App\Entity\InputFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InputFile>
 *
 * @method InputFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputFile[]    findAll()
 * @method InputFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputFile::class);
    }
}
