<?php

namespace App\Support\Yaml;

use Illuminate\Filesystem\Filesystem;

class BlocksYaml extends GroupedSetsYaml
{
    public function __construct(Filesystem $files)
    {
        parent::__construct(
            $files,
            config('statamic.bedrock.scaffold.fieldsets_path').'/blocks.yaml',
            'blocks'
        );
    }
}
