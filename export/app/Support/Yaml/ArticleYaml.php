<?php

namespace App\Support\Yaml;

use Illuminate\Filesystem\Filesystem;

class ArticleYaml extends GroupedSetsYaml
{
    public function __construct(Filesystem $files)
    {
        parent::__construct($files, 'resources/fieldsets/article.yaml', 'article');
    }
}
