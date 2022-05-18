<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Interface\GameInterface;
use App\Entity\Interface\ParticipantInterface;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinColumns;
use phpDocumentor\Reflection\Types\Nullable;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource]
class Game implements GameInterface
{
    public const STATE_NEW = 'new';
    public const STATE_WAITING_PARTICIPANTS = 'waiting_participants';
    public const STATE_STARTED = 'started';
    public const STATE_FINISHED = 'finished';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToMany(targetEntity:'participant')] // link to entity -> participant table
    #[ORM\JoinTable(name:'players')] // join table name
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id')] // column from game table
    #[ORM\InverseJoinColumn(name:'player_id', referencedColumnName:'id', unique:true)] // column from participant table
    private Collection $participants;

    #[ORM\Column(type: 'string', length:30)]
    private string $state = self::STATE_NEW;

    #[ORM\OneToOne(targetEntity:Participant::class)]
    #[ORM\JoinColumn(name:"active_player_turn", nullable:true, referencedColumnName:"id")]
    private ParticipantInterface $activePlayerTurn;


    #[ORM\OneToOne(targetEntity: 'Map')]
    #[ORM\JoinColumn(name:'map_id', referencedColumnName:'id', nullable:true)]
    private ?Map $map;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->map = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(ParticipantInterface $participant): GameInterface
    {
        $this->participants->add($participant);

        return $this;
    }

    public function getWhosTurn(): ?ParticipantInterface
    {
        return $this->activePlayerTurn;
    }

    public function activatePlayer(ParticipantInterface $participant): self
    {
        $this->activePlayerTurn = $participant;

        return $this;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(Map $map): self
    {
        $this->map = $map;

        return $this;
    }

}
