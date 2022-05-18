<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MapRepository::class)]
#[ApiResource()]
class Map
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $height;

    #[ORM\Column(type: 'integer')]
    private $width;

    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Cell::class, orphanRemoval: true)]
    private $cells;

    public function __construct()
    {
        $this->cells = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return Collection<int, Cell>
     */
    public function getCells(bool $available = false): Collection
    {
        if ($available) {
            return $this->cells->filter(function (Cell $cell) {
                if (!$cell->hasPlayerInCell() && $cell->isRegularCell()) {
                    return true;
                }

                return false;
            });
        }
        return $this->cells;
    }

    public function getCellMatrix(): array
    {
        $matrix = array_fill(0, $this->width, array_fill(0, $this->height, []));

        /** @var Cell $cell */
        foreach($this->cells as $cell) {
            $matrix[$cell->getCoordY()][$cell->getCoordX()] = [
                'player' => $cell->getPlayer() ? $cell->getPlayer()->getName() : '',
                 'type' => $cell->getType(),
                 'coords' => [
                     'x' => $cell->getCoordX(),
                     'y' => $cell->getCoordY(),
                 ]
            ];
        }

        return $matrix;
    }

    public function addCell(Cell $cell): self
    {
        if (!$this->cells->contains($cell)) {
            $this->cells[] = $cell;
            $cell->setMap($this);
        }

        return $this;
    }

    public function removeCell(Cell $cell): self
    {
        if ($this->cells->removeElement($cell)) {
            // set the owning side to null (unless already changed)
            if ($cell->getMap() === $this) {
                $cell->setMap(null);
            }
        }

        return $this;
    }
}
