<?php

declare(strict_types=1);

namespace App\Utils;

class NameToCodeConverter
{
    public function convert(string $name): string
    {
        return strtolower(str_replace(' ', '_', $name));
    }
}