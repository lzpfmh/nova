<?php
/**
 * Client for swoole
 * User: moyo
 * Date: 9/28/15
 * Time: 3:11 PM
 */

namespace Kdt\Iron\Nova\Network\Client;

use Config;

use Kdt\Iron\Nova\Exception\NetworkException;
use swoole_client as SwooleClient;

class Swoole
{
    /**
     * @var string
     */
    private $connConfKey = 'nova.client';

    /**
     * @var string
     */
    private $swooleConfKey = 'nova.swoole.client';

    /**
     * @var object
     */
    private $client = null;

    /**
     * @var bool
     */
    private $idle = false;

    /**
     * Swoole constructor.
     */
    public function __construct()
    {
        $connConf = Config::get($this->connConfKey);
        $clientFlags = $connConf['persistent'] ? SWOOLE_SOCK_TCP | SWOOLE_KEEP : SWOOLE_SOCK_TCP;
        $this->client = new SwooleClient($clientFlags);
        $this->client->set(Config::get($this->swooleConfKey));
        $connected = $this->client->connect($connConf['host'], $connConf['port'], $connConf['timeout']);
        if ($connected)
        {
            $this->setIdling();
        }
        else
        {
            throw new NetworkException(socket_strerror($this->client->errCode), $this->client->errCode);
        }
    }

    /**
     * @param $serviceName
     * @param $methodName
     * @param $thriftBIN
     * @throws NetworkException
     */
    public function send($serviceName, $methodName, $thriftBIN)
    {
        $this->setBusying();
        $sent = $this->client->call_service($serviceName, $methodName, '{}', $thriftBIN);
        if (false === $sent)
        {
            throw new NetworkException(socket_strerror($this->client->errCode), $this->client->errCode);
        }
    }

    /**
     * @return string
     * @throws NetworkException
     */
    public function recv()
    {
        $response = $this->client->recv_service();
        if (false === $response)
        {
            throw new NetworkException(socket_strerror($this->client->errCode), $this->client->errCode);
        }
        $this->setIdling();
        return $response;
    }

    /**
     * @return bool
     */
    public function idle()
    {
        return $this->idle;
    }

    /**
     * set client idling
     */
    private function setIdling()
    {
        $this->idle = true;
    }

    /**
     * set client busying
     */
    private function setBusying()
    {
        $this->idle = false;
    }
}