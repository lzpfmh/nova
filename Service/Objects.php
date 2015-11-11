<?php
/**
 * Service objects mgr
 * User: moyo
 * Date: 9/23/15
 * Time: 2:42 PM
 */

namespace Kdt\Iron\Nova\Thrift\Service;

use Kdt\Iron\Nova\Thrift\Foundation\Traits\InstanceManager;

class Objects
{
    /**
     * Instance mgr
     */
    use InstanceManager;

    /**
     * @var array
     */
    private $objectCache = [];

    /**
     * @param $className
     * @return mixed
     */
    public function load($className)
    {
        if (isset($this->objectCache[$className]))
        {
            $object = $this->objectCache[$className];
        }
        else
        {
            $this->objectCache[$className] = $object = new $className();
        }
        return $object;
    }
}