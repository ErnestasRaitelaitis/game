<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CellRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CellRepository::class)]
#[ApiResource()]
class Cell
{
    public const TYPE_REGULAR = 'regular';
    public const TYPE_WALL = 'wall';
    public const TYPE_EXIT = 'exit';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $type;

    #[ORM\OneToOne(targetEntity: Participant::class, cascade: ['persist', 'remove'])]
    private $player;

    #[ORM\ManyToOne(targetEntity: Map::class, inversedBy: 'cells')]
    #[ORM\JoinColumn(nullable: false)]
    private $map;

    #[ORM\Column(name: 'coord_X', type: 'integer')]
    private $coordX;

    #[ORM\Column(name: 'coord_Y', type: 'integer')]
    private $coordY;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isRegularCell(): bool
    {
        return $this->type === Cell::TYPE_REGULAR;
    }

    public function isExit(): bool
    {
        return $this->type === Cell::TYPE_EXIT;
    }

    public function getPlayer(): ?Participant
    {
        return $this->player;
    }

    public function hasPlayerInCell(): bool
    {
        return $this->player !== null;
    }

    public function setPlayer(?Participant $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getCoordX(): int
    {
        return $this->coordX;
    }

    public function setCoordX(int $coordX): self
    {
        $this->coordX = $coordX;

        return $this;
    }

    public function getCoordY(): int
    {
        return $this->coordY;
    }

    public function setCoordY(int $coordY): self
    {
        $this->coordY = $coordY;

        return $this;
    }
}
