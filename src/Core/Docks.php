<?php

namespace Timpack\TvshowTracker\Core;

use Guzzle\Http\Exception\CurlException;
use LogicException;
use Monolog\Logger;
use Timpack\TvshowTracker\Tv\Helper\Helper;
use TPB\API;
use Vohof\Transmission;

/**
 * @link https://trac.transmissionbt.com/browser/trunk/extras/rpc-spec.txt
 */
class Docks
{

    /**
     * @var API
     */
    protected $_api;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var Transmission
     */
    protected $_transmission;

    /**
     * @var array
     */
    protected $_shipments = [];

    /**
     * @var array
     */
    protected $_cache = [];

    /**
     * @var array
     */
    protected $_config;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * Docks constructor.
     * @param $config
     * @param Logger $logger
     */
    public function __construct($config, Logger $logger)
    {
        $this->_config = $config;
        $this->_logger = $logger;

        $this->_api = new API();
        $this->_helper = new Helper();
        $this->_transmission = new Transmission($config);

        try {
            $this->_shipments = $this->fetchShipments();
        } catch (CurlException $e) {
            throw new LogicException('Failed to connect to Transmission. Check if your credentials are correct and if the server is running the specified port.');
        }
    }

    /**
     * @param $showName
     * @param $seasonNumber
     * @param $episodeNumber
     * @return array
     */
    public function findEpisode($showName, $seasonNumber, $episodeNumber)
    {
        $query = $this->_helper->getEpisodeString($showName, $seasonNumber, $episodeNumber);

        if (isset($this->_cache[$query])) {
            return $this->_cache[$query];
        }

        $searchResults = $this->_api->searchByTitle($query);
        $searchResults = array_change_key_case((array) $searchResults);

        if (isset($searchResults[0])) {
            $topResult = $searchResults[0];
            $this->_cache[$query] = $topResult;
            return $topResult;
        }
        return [];
    }

    /**
     * @param $showName
     * @param $seasonNumber
     * @param $episodeNumber
     * @return bool
     */
    public function isShipping($showName, $seasonNumber, $episodeNumber)
    {
        $pattern = $this->_helper->getEpisodePattern($showName, $seasonNumber, $episodeNumber);
        foreach ($this->_shipments as $_shipment) {
            if (preg_match($pattern, $_shipment['name'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $entry
     * @param Vault $vault
     */
    public function transmitEpisode($entry, Vault $vault)
    {
        $this->_transmission->add($entry->Magnet);
    }

    /**
     * @return mixed
     */
    public function fetchShipments()
    {
        $result = $this->_transmission->get('all');
        return $result['torrents'];
    }

}