<?php

namespace App\Tags;

use Statamic\Tags\Tags;

class YoutubeId extends Tags
{
    /**
     * The {{ youtube_id }} tag.
     *
     * @return string|array
     */
    public function index()
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

        if (empty($youtubeUrl)) {
            return false;
        }

        // Checks if it matches a pattern and returns the value
        if (preg_match($pattern, $youtubeUrl, $match)) {
            return $match[1];
        }

        // if no match return false.
        return false;
    }
}
