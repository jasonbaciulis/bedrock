<?php

declare(strict_types=1);

namespace App\Tags;

use Statamic\Tags\Tags;

final class YoutubeId extends Tags
{
    /**
     * The {{ youtube_id }} tag.
     */
    public function index(): string|false
    {
        /**
         * https://gist.github.com/leogopal/b429f9700d473a55f70819dc6e5195f0
         * Pattern matches
         * http://youtu.be/ID
         * http://www.youtube.com/embed/ID
         * http://www.youtube.com/watch?v=ID
         * http://www.youtube.com/?v=ID
         * http://www.youtube.com/v/ID
         * http://www.youtube.com/e/ID
         * http://www.youtube.com/user/username#p/u/11/ID
         * http://www.youtube.com/leogopal#p/c/playlistID/0/ID
         * http://www.youtube.com/watch?feature=player_embedded&v=ID
         * http://www.youtube.com/?feature=player_embedded&v=ID
         */
        $pattern =
            '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';

        $youtubeUrl = $this->params->get('youtube_url');

        if (! is_string($youtubeUrl)) {
            return false;
        }

        if (preg_match($pattern, $youtubeUrl, $match)) {
            return $match[1];
        }

        return false;
    }
}
