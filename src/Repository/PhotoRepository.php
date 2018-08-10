<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function findByStatus($status, $page = 1, $limit = 12)
    {
        $page = $page > 0 ? $page : 1;

        return $this->createQueryBuilder('p')
            ->andWhere("p.status = '" . $status . "'")
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findOneById($id): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function randomPhoto(): ?Photo
    {
        $allPhotos = $this->createQueryBuilder('p')
            ->andWhere("p.status = 'approved'")
            ->getQuery()
            ->getResult();

        shuffle($allPhotos);

        return $allPhotos[0];
    }
}
