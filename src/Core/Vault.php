<?php

namespace Timpack\TvshowTracker\Core;

use Symfony\Component\Filesystem\Filesystem;
use Timpack\TvshowTracker\Tv\Helper\Helper;

class Vault
{

    /**
     * @var Filesystem
     */
    protected $_fs;

    /**
     * @var string
     */
    protected $_vaultPath;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Vault constructor.
     */
    public function __construct($vaultPath)
    {
        $this->_vaultPath = rtrim($vaultPath, '/');
        $this->_fs = new Filesystem();
        $this->_helper = new Helper();
    }

    /**
     * @param $showName
     * @param $seasonNumber
     * @param $episodeNumber
     * @return bool
     */
    public function findEpisode($showName, $seasonNumber, $episodeNumber)
    {
        $episodeString = $this->_helper->getEpisodeString($showName, $seasonNumber, $episodeNumber);
        return $this->_fs->exists("$this->_vaultPath/$episodeString");
    }

}