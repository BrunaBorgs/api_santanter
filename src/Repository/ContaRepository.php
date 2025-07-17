<?php

namespace App\Repository;

use App\Entity\Conta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conta>
 */
class ContaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conta::class);
    }

    /**
     * @return ?Conta
     */
    public function findByUsuarioId($usuarioId): ?Conta
    {
        return $this->createQueryBuilder('c')
            ->join('c.usuario', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $usuarioId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /**
     * @return Conta[]
     */
    public function findByFiltro(string $valor) {
        $q = $this->createQueryBuilder('c');

        return $q
            ->join('c.usuario', 'u')
            ->where($q->expr()->like('u.nome', ':valor'))
            ->orWhere($q->expr()->like('u.email', ':valor'))
            ->orWhere($q->expr()->like('u.cpf', ':valor'))
            ->orWhere($q->expr()->like('u.telefone', ':valor'))
            ->orWhere($q->expr()->like('c.numero', ':valor'))
            ->setParameter('valor', "%$valor%")
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Conta
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
