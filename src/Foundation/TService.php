<?php
/**
 * Abs TService
 * User: moyo
 * Date: 9/14/15
 * Time: 8:55 PM
 */

namespace Kdt\Iron\Nova\Foundation;

use Kdt\Iron\Nova\Foundation\Traits\InstanceManager;
use Kdt\Iron\Nova\Transport\Client;

abstract class TService
{
    /**
     * Instance mgr
     */
    use InstanceManager;

    /**
     * @var Client
     */
    private $client = null;

    /**
     * @var TSpecification
     */
    private $relatedSpec = null;

    /**
     * @return TSpecification
     */
    abstract protected function specificationProvider();

    /**
     * @param $method
     * @param $args
     * @return array
     */
    final public function getInputStructSpec($method, $args = [])
    {
        $spec = $this->getRelatedSpec()->getInputStructSpec($method);
        foreach ($args as $i => $arg)
        {
            $spec[$i + 1]['value'] = $arg;
        }
        return $spec;
    }

    /**
     * @param $method
     * @return array
     */
    final public function getOutputStructSpec($method)
    {
        return $this->getRelatedSpec()->getOutputStructSpec($method);
    }

    /**
     * @param $method
     * @return array
     */
    final public function getExceptionStructSpec($method)
    {
        return $this->getRelatedSpec()->getExceptionStructSpec($method);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    final protected function apiCall($method, $arguments)
    {
        return $this->getClient()->call($method, $this->getInputStructSpec($method, $arguments), $this->getOutputStructSpec($method), $this->getExceptionStructSpec($method));
    }

    /**
     * @return Client
     */
    final private function getClient()
    {
        if (is_null($this->client))
        {
            $this->client = new Client($this->getRelatedSpec()->getServiceName());
        }
        return $this->client;
    }

    /**
     * @return TSpecification
     */
    final private function getRelatedSpec()
    {
        if (is_null($this->relatedSpec))
        {
            $this->relatedSpec = $this->specificationProvider();
        }
        return $this->relatedSpec;
    }
}