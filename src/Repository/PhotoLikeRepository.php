<?php

namespace App\Repository;

use App\Entity\PhotoLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PhotoLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoLike[]    findAll()
 * @method PhotoLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoLikeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PhotoLike::class);
    }

//    /**
//     * @return PhotoLike[] Returns an array of PhotoLike objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhotoLike
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
