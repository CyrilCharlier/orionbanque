<?php

namespace App\Repository;

use App\Entity\Tiers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tiers>
 *
 * @method Tiers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tiers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tiers[]    findAll()
 * @method Tiers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TiersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tiers::class);
    }

    public function add(Tiers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tiers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
