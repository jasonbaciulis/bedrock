<?php

declare(strict_types=1);

test('every published page and the latest post load without javascript errors', function (): void {
    $pages = visit(smokeTestUrls());

    $pages->assertNoJavaScriptErrors();
});

test('every published page and the latest post have no accessibility issues', function (): void {
    $pages = visit(smokeTestUrls());

    $pages->assertNoAccessibilityIssues();
});
