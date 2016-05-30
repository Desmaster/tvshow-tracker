<?php

require_once('vendor/autoload.php');

$configPath = __DIR__ . '/config.php';

if (!file_exists($configPath)) {
    echo "$configPath required";
    exit;
}

$config = include($configPath);

$docks = new \Timpack\TvshowTracker\Docks($config['transmission']);
$streets = new \Timpack\TvshowTracker\Streets($config['tmdb']);
$vault = new \Timpack\TvshowTracker\Vault(__DIR__ . '/var');
$watchList = $streets->getWatchlist();

$docks->fetchShipments();

/**
 * @var array $_show
 */
foreach ($watchList['results'] as $_show) {
    $showId = $_show['id'];
    $showName = $_show['name'];
    $seasons = $streets->getSeasonsByShow($showId);
    foreach ($seasons as $_season) {
        $seasonNumber = $_season['season_number'];
        if (!$seasonNumber) {
            continue;
        }
        $episodes = $streets->getEpisodesBySeason($showId, $seasonNumber);
        foreach ($episodes as $_episode) {
            $episodeNumber = $_episode['episode_number'];

            $date = new DateTime($_episode['air_date']);

            // Episode has not been aired yet
            if ($date->getTimestamp() > time()) {
                continue;
            }

            // Episode already exists in vault
            if ($vault->findEpisode($showName, $seasonNumber, $episodeNumber)) {
                continue;
            }

            // If episode is already being shipped
            if ($docks->isShipping($showName, $seasonNumber, $episodeNumber)) {
                continue;
            }

            if ($entry = $docks->findEpisode($showName, $seasonNumber, $episodeNumber)) {
                $docks->transmitEpisode($entry, $vault);
            } else {
                // TODO: Log something like "Couldn't find episode in the docks"
            }
        }
    }
}
