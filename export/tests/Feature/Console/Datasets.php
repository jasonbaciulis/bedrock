<?php

declare(strict_types=1);

use Tests\Feature\Console\Support\ScaffoldFixture;

dataset('scaffolds', [
    'block' => [ScaffoldFixture::Block],
    'set' => [ScaffoldFixture::ArticleSet],
]);
