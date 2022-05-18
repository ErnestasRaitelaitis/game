<?php

declare(strict_types=1);

namespace App\CliCommand;

use App\Entity\Cell;
use App\Entity\Map;
use App\Repository\CellRepository;
use App\Repository\MapRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDefaultMap extends Command
{
    /** @var string */
    protected static $defaultName = 'app:map:create';
    private MapRepository $mapRepository;
    private CellRepository $cellRepository;
    private EntityManagerInterface $em;


    public function __construct(MapRepository $mapRepository, CellRepository $cellRepository, EntityManagerInterface $em)
    {
        parent::__construct();

        $this->mapRepository = $mapRepository;
        $this->cellRepository = $cellRepository;
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create default map.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates default map.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $map = $this->createMapEntity();
        $this->createCellsForMap($map);

        $this->em->flush();

        return 0;
    }

    private function createMapEntity(): Map
    {
        $map = new Map();
        $map->setHeight(7);
        $map->setWidth(7);

        $this->mapRepository->add($map);

        return $map;
    }

    private function createCellsForMap(Map $map): void
    {
        $cellTypes = [
            Cell::TYPE_WALL, Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,
            Cell::TYPE_WALL, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_WALL,
            Cell::TYPE_EXIT, Cell::TYPE_REGULAR, Cell::TYPE_WALL,    Cell::TYPE_REGULAR, Cell::TYPE_WALL,    Cell::TYPE_REGULAR, Cell::TYPE_EXIT,
            Cell::TYPE_WALL, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_WALL,
            Cell::TYPE_WALL, Cell::TYPE_REGULAR, Cell::TYPE_WALL,    Cell::TYPE_REGULAR, Cell::TYPE_WALL,    Cell::TYPE_REGULAR, Cell::TYPE_WALL,
            Cell::TYPE_WALL, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_REGULAR, Cell::TYPE_WALL,
            Cell::TYPE_WALL, Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,    Cell::TYPE_WALL,
        ];
        $x = 0;
        $y = 0;

        while (count($cellTypes) > 0) {
            $cell = new Cell();
            $cell->setType(array_shift($cellTypes));
            $cell->setPlayer(null);
            $cell->setMap($map);
            $cell->setCoordX($x);
            $cell->setCoordY($y);
            $this->cellRepository->add($cell);
            [$x, $y] = $this->getCorrectCoords($x, $y, $map->getWidth(), $map->getHeight());
        }
    }

    private function getCorrectCoords(int $currentX, int $currentY, int $maxX, int $maxY): array
    {
        return [
            $currentX + 1 === $maxX ? 0 : $currentX + 1,
            $currentX + 1 === $maxX ? $currentY + 1 : $currentY,
        ];
    }
}