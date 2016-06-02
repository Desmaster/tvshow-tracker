<?php

namespace Timpack\TvshowTracker\Tv\Helper;

class Helper
{

    /**
     * @param $showName
     * @param $seasonNumber
     * @param $episodeNumber
     * @return mixed|string
     */
    public function getEpisodeString($showName, $seasonNumber, $episodeNumber)
    {
        $seasonNumber = ($seasonNumber > 9 ?: "0$seasonNumber");
        $episodeNumber = ($episodeNumber > 9 ?: "0$episodeNumber");

        $query = "$showName S{$seasonNumber}E{$episodeNumber}";
        $query = str_replace(' ', '.', $query);
        return $query;
    }

    /**
     * @param $showName
     * @param $seasonNumber
     * @param $episodeNumber
     * @return string
     */
    public function getEpisodePattern($showName, $seasonNumber, $episodeNumber)
    {
        $showName = str_replace(" ", "[^a-zA-Z]+", $showName);
        $seasonNumber = ($seasonNumber > 9 ?: "0$seasonNumber");
        $episodeNumber = ($episodeNumber > 9 ?: "0$episodeNumber");
        return "/{$showName}[^a-zA-Z]*S{$seasonNumber}E{$episodeNumber}/i";
    }

}