<?php

declare(strict_types=1);

namespace App\Builder;

use App\Builder\Interface\ParticipantBuilderInterface;
use App\Entity\Interface\ParticipantInterface;
use App\Entity\Participant;
use App\Utils\NameToCodeConverter;

class ParticipantBuilder implements ParticipantBuilderInterface
{
    private NameToCodeConverter $nameToCodeConverter;

    public function __construct(NameToCodeConverter $nameToCodeConverter)
    {
        $this->nameToCodeConverter = $nameToCodeConverter;
    }


    public function build(string $name): ParticipantInterface
    {
        $participant = new Participant();

        $participant->setName($name);
        $participant->setCode($this->nameToCodeConverter->convert($name));
        
        return $participant;
    }
}
