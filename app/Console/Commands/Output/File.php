<?php

namespace App\Console\Commands\Output;

use Stringable;
use Tighten\Ziggy\Ziggy;

class File implements Stringable
{
    protected $ziggy;

    public function __construct(Ziggy $ziggy)
    {
        $this->ziggy = $ziggy;
    }

    public function __toString(): string
    {
        return <<<JSON
{$this->ziggy->toJson(JSON_PRETTY_PRINT)}
JSON;
    }
}
