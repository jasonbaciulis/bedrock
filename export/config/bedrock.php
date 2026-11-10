<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Control Panel Toolbar
    |--------------------------------------------------------------------------
    |
    | Show the "Edit this entry" toolbar on the front-end to users who can
    | edit the current entry.
    |
    */

    'cp_toolbar_enabled' => (bool) env('BEDROCK_CP_TOOLBAR_ENABLED', true),

];
