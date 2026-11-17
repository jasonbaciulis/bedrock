<?php

declare(strict_types=1);

test('every published page and the latest post render', function (): void {
    foreach (smokeTestUrls() as $url) {
        $this->get($url)->assertOk();
    }
});
