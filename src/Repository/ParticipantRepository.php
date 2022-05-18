<?php

namespace App\Repository;

use App\Builder\ParticipantBuilder;
use App\Entity\Interface\ParticipantInterface;
use App\Entity\Participant;
use App\Utils\NameToCodeConverter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participant>
 *
 */
class ParticipantRepository extends ServiceEntityRepository
{
    private ParticipantBuilder $participantBuilder;
    private NameToCodeConverter $nameToCodeConverter;

    public function __construct(ManagerRegistry $registry, ParticipantBuilder $participantBuilder, NameToCodeConverter $nameToCodeConverter)
    {
        parent::__construct($registry, Participant::class);

        $this->participantBuilder = $participantBuilder;
        $this->nameToCodeConverter = $nameToCodeConverter;
    }

    public function findOneByName(string $name): ?ParticipantInterface
    {
        $code = $this->nameToCodeConverter->convert($name);
        
        return $this->findOneBy(['code' => $code]);
    }

    public function findOneOrCreateByName(string $name): ?ParticipantInterface
    {
        $participant = $this->findOneByName($name);

        if (!$participant) {
            $participant = $this->participantBuilder->build($name);

            $this->add($participant, true);
        }
        
        return $participant;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Participant $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Participant $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

//    /**
//     * @return Participant[] Returns an array of Participant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Participant
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
