<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Interface\GameInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Interface\ParticipantInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Builder\Interface\GameBuilderInterface;
use App\Utils\RandomizePositions;
use Doctrine\ORM\Query\AST\Join;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    private GameBuilderInterface $builder;

    public function __construct(ManagerRegistry $registry, GameBuilderInterface $builder, RandomizePositions $randomizePositions)
    {
        parent::__construct($registry, Game::class);

        $this->builder = $builder;
        $this->randomizePositions = $randomizePositions;
    }

    public function findActiveOneWithParticipant(ParticipantInterface $participant): ?GameInterface
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.participants', 'p')
            ->where('g.state IN (:activeStates)')
            ->andWhere('p.id = :playerId')
            ->setParameter('playerId', $participant)
            ->setParameter('activeStates', [Game::STATE_NEW, Game::STATE_WAITING_PARTICIPANTS, Game::STATE_STARTED])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOpenGameForPlayer(ParticipantInterface $participant): ?GameInterface
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.participants', 'p')
            ->where('g.state IN (:activeStates)')
            ->andWhere('p.id != :playerId')
            ->setParameter('playerId', $participant)
            ->setParameter('activeStates', [Game::STATE_NEW, Game::STATE_WAITING_PARTICIPANTS])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOrCreateGameForParticipant(ParticipantInterface $participant): GameInterface
    {
        $game = $this->findOpenGameForPlayer($participant);

        if (!$game) {
            $game = $this->builder->build($participant);
            $this->add($game);

            return $game;
        }

        $game->addParticipant($participant);

        if ($game->getParticipants()->count() === 2) { // if two players already joined the game marke it as started    
            $game->setState(Game::STATE_STARTED);
        }

        return $game;
    }

    public function findStartedGameById(int $id): ?GameInterface
    {
        return $this->findOneBy(['state' => Game::STATE_STARTED, 'id' => $id]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Game $entity, bool $flush = false): void
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
    public function remove(Game $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
