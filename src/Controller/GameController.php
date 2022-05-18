<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\ParticipantRepository;
use App\Builder\GameResponse;
use App\Entity\Game;
use App\Repository\MapRepository;
use App\Utils\MapForGame;
use App\Utils\RandomizePositions;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private ParticipantRepository $participantRrepository;
    private GameRepository $gameRepository;
    private GameResponse $gameResponse;
    private RandomizePositions $randomizePositions;
    private MapForGame $mapForGame;
    private EntityManagerInterface $entityManager;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRrepository,
        GameRepository $gameRepository,
        GameResponse $gameResponse,
        MapForGame $mapForGame,
        RandomizePositions $randomizePositions
    ) {
        $this->participantRrepository = $participantRrepository;
        $this->gameRepository = $gameRepository;
        $this->gameResponse = $gameResponse;
        $this->randomizePositions = $randomizePositions;
        $this->mapForGame = $mapForGame;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/get-active-game', name: 'get_active_game', methods: ["GET"])]
    public function getActiveGame(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $game = $this->gameRepository->find($parameters['id']);

        return $this->json($this->gameResponse->build($game));
    }

    #[Route('/api/create-game', name: 'create_game', methods: ["POST"])]
    public function createGame(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        
        $participant = $this->participantRrepository->findOneOrCreateByName(
            $this->getParticipantNameOrThrowException($parameters)
        );

        //find or create game for participant
        $game = $this->gameRepository->findActiveOneWithParticipant($participant);

        // check if participant has an open game with him
        if ($game) {
            return $this->json(
                array_merge(
                    [],
                    ['warning-message' => sprintf('Player "%s" already has an open game with id: %d', $participant->getName(), $game->getId())],
                    $this->gameResponse->build($game))
            );
        }

        // find existing open game without this participant or create new game
        $game = $this->gameRepository->findOrCreateGameForParticipant($participant);

        if (!$game->getMap()) {
            $map = $this->mapForGame->selectRandom();
            if (!$map) {
                return $this->json(['warning-message' => 'There is no available maps. Game can not be created']);
            }

            $game->setMap($map);
        }
        

        if ($game->getState() === Game::STATE_STARTED) {
            $this->randomizePositions->randomizeInitialPlayerPositions($game);
        }

        //flush everything to DB
        $this->entityManager->flush();

        return $this->json($this->gameResponse->build($game));
    }


    #[Route('/api/game/{gameId}/make-move', name: 'make_move', methods:["PUT"])]
    public function makeMove(Request $request, int $gameId): Response
    {
        $game = $this->gameRepository->findStartedGameById($gameId);

        if (!$game) {
            throw new BadRequestException(sprintf('There is no active game with id: %d', $gameId));
        }

        $parameters = json_decode($request->getContent(), true);

        $participant = $this->participantRrepository->findOneByName(
            $this->getParticipantNameOrThrowException($parameters)
        );

        if (!$participant) {
            throw new BadRequestException(sprintf('%s participant does not exist', $parameters['participant']));
        }

        if (!$game->getParticipants()->contains($participant)) {
            throw new BadRequestException(sprintf('%s participant does not belong to current game', $participant->getName()));
        }

        return $this->json(['move']);
    }

    private function getParticipantNameOrThrowException(array $parameters): string
    {
        if (!array_key_exists('participant', $parameters)) {
            throw new BadRequestException('Missing "participant" argument');
        }
        $participantName = $parameters['participant'];
        
        if (!is_string($participantName) || trim($participantName) === '') {
            throw new BadRequestException('participant name should be provided');
        }

        return $participantName;
    }
}
