<?php

namespace App\Support\Yaml;

use Illuminate\Filesystem\Filesystem;

class BlocksYaml extends GroupedSetsYaml
{
    public function __construct(Filesystem $files)
    {
        parent::__construct($files, 'resources/fieldsets/blocks.yaml', 'blocks');
    }
}
