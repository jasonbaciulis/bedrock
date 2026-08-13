<?php

declare(strict_types=1);

use App\Tags\YoutubeId;

function youtubeIdFor(?string $url): string|false
{
    $tag = new YoutubeId;
    $tag->setContext([]);
    $tag->setParameters($url === null ? [] : ['youtube_url' => $url]);

    return $tag->index();
}

test('the youtube_id tag extracts the id from every supported url shape', function (string $url): void {
    expect(youtubeIdFor($url))->toBe('dQw4w9WgXcQ');
})->with([
    'short link' => 'http://youtu.be/dQw4w9WgXcQ',
    'embed' => 'http://www.youtube.com/embed/dQw4w9WgXcQ',
    'watch' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'query only' => 'http://www.youtube.com/?v=dQw4w9WgXcQ',
    'v path' => 'http://www.youtube.com/v/dQw4w9WgXcQ',
    'e path' => 'http://www.youtube.com/e/dQw4w9WgXcQ',
    'user playlist' => 'http://www.youtube.com/user/username#p/u/11/dQw4w9WgXcQ',
    'player embedded' => 'http://www.youtube.com/watch?feature=player_embedded&v=dQw4w9WgXcQ',
    'no-cookie' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
]);

test('the youtube_id tag returns false when the url has no id', function (?string $url): void {
    expect(youtubeIdFor($url))->toBeFalse();
})->with([
    'missing parameter' => null,
    'empty parameter' => '',
    'not a youtube url' => 'https://vimeo.com/123456789',
]);
