<?php

namespace Timpack\TvshowTracker\Tv\Command;

use DateTime;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Timpack\TvshowTracker\Core\Docks;
use Timpack\TvshowTracker\Core\Streets;
use Timpack\TvshowTracker\Core\Vault;

class SyncCommand extends Command
{

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var Docks
     */
    protected $_docks;

    /**
     * @var Streets
     */
    protected $_streets;

    /**
     * @var Vault
     */
    protected $_vault;

    /**
     * Track constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_logger = new Logger('main');
        $this->_logger->pushHandler(
            new StreamHandler('php://stdout', Logger::DEBUG)
        );

        $config = new Config(TPR . '/config/tv.json');
        $this->_streets = new Streets($config->get('tmdb'));
        $this->_docks = new Docks($config->get('transmission'), $this->_logger);
        $this->_vault = new Vault('/');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tv:sync')
            ->setDescription('Sync tv shows');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $watchList = $this->_streets->getWatchlist();

        /**
         * @var array $_show
         */
        foreach ($watchList['results'] as $_show) {
            $showId = $_show['id'];
            $showName = $_show['name'];
            $seasons = $this->_streets->getSeasonsByShow($showId);
            foreach ($seasons as $_season) {
                $seasonNumber = $_season['season_number'];
                if (!$seasonNumber) {
                    continue;
                }
                $episodes = $this->_streets->getEpisodesBySeason($showId, $seasonNumber);
                foreach ($episodes as $_episode) {
                    $episodeNumber = $_episode['episode_number'];

                    $date = new DateTime($_episode['air_date']);

                    // Episode has not been aired yet
                    if ($date->getTimestamp() > time()) {
                        continue;
                    }

                    // Episode already exists in vault
                    if ($this->_vault->findEpisode($showName, $seasonNumber, $episodeNumber)) {
                        continue;
                    }

                    // If episode is already being shipped
                    if ($this->_docks->isShipping($showName, $seasonNumber, $episodeNumber)) {
                        continue;
                    }

                    if ($entry = $this->_docks->findEpisode($showName, $seasonNumber, $episodeNumber)) {
                        $this->_docks->transmitEpisode($entry, $this->_vault);
                    } else {
                        $context = ['showName' => $showName, 'seasonNumber' => $seasonNumber, 'episodeNumber' => $episodeNumber];
                        $this->_logger->info("Failed to find episode", $context);
                    }
                }
            }
        }
    }

}