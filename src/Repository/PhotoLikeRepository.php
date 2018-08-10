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

    public function findById($id): ?PhotoLike
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function findByPhotoAndUser($photo, $user): ?PhotoLike
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.photo_id = :photo')
            ->setParameter('photo', $photo)
            ->andWhere('p.user_id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
