<?php

declare(strict_types=1);

test('homepage loads without javascript errors', function (): void {
    $homepage = visit(['/']);

    $homepage->assertNoJavaScriptErrors();
});

test('homepage has no accessibility issues', function (): void {
    $homepage = visit(['/']);

    $homepage->assertNoAccessibilityIssues();
});
