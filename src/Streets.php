<?php

namespace Timpack\TvshowTracker;

use DateTime;
use Tmdb\ApiToken;
use Tmdb\Client;
use Tmdb\Model\Account;
use Tmdb\RequestToken;

class Streets
{

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_password;

    /**
     * @var string
     */
    protected $_apiToken;

    /**
     * @var string
     */
    protected $_requestToken;

    /**
     * @var string
     */
    protected $_sessionId;


    /**
     * @var Client
     */
    protected $_client;

    /**
     * Streets constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->_username = $config['username'];
        $this->_password = $config['password'];
        $this->_apiToken = $config['apitoken'];

        $this->_initialize();
    }

    /**
     * Initialize api client, tokens and session
     */
    protected function _initialize()
    {
        $this->_apiToken = new ApiToken($this->_apiToken);
        $this->_client = new Client($this->_apiToken);

        $authentication = $this->_client->getAuthenticationApi();
        $requestTokenResults = $authentication->getNewToken();

        $this->_requestToken = new RequestToken($requestTokenResults['request_token']);
        $this->_requestToken->setExpiresAt(new DateTime($requestTokenResults['expires_at']));
        $this->_requestToken->setSuccess($requestTokenResults['success']);

        $sessionTokenResults = $authentication->getSessionTokenWithLogin(
            $this->_requestToken,
            $this->_username,
            $this->_password
        );

        $this->_sessionId = $sessionTokenResults['session_id'];
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        $accountApi = $this->_client->getAccountApi();
        $account = new Account();
        $accountData = $accountApi->getAccount(['session_id' => $this->_sessionId]);

        foreach ($accountData as $_key => $_value) {
            call_user_func(array($account, 'set' . strtoupper(str_replace('_', '', $_key))), $_value);
        }

        return $account;
    }

    /**
     * @return mixed
     */
    public function getWatchlist()
    {
        $accountApi = $this->_client->getAccountApi();
        $account = $this->getAccount();
        return $accountApi->getTvWatchlist($account->getId(), ['session_id' => $this->_sessionId]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSeasonsByShow($id)
    {
        $tvApi = $this->_client->getTvApi();
        $result = $tvApi->getTvshow($id);
        return $result['seasons'];
    }

    /**
     * @param $showId
     * @param $seasonNumber
     * @return mixed
     */
    public function getEpisodesBySeason($showId, $seasonNumber)
    {
        $seasonApi = $this->_client->getTvSeasonApi();
        $result = $seasonApi->getSeason($showId, $seasonNumber);
        return $result['episodes'];
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->_client;
    }

}