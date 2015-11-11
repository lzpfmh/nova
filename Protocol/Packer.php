<?php
/**
 * Thrift protocol packer
 * User: moyo
 * Date: 9/23/15
 * Time: 11:36 AM
 */

namespace Kdt\Iron\Nova\Thrift\Protocol;

use Kdt\Iron\Nova\Thrift\Foundation\Traits\InstanceManager;
use Kdt\Iron\Nova\Thrift\Protocol\Packer\Extension;
use Kdt\Iron\Nova\Thrift\Protocol\Packer\Native;
use Exception as SysException;

class Packer
{
    /*
     * Instance mgr
     */
    use InstanceManager;

    /**
     * @var string
     */
    public $successKey = 'success';

    /**
     * @var Packer\Abstracts
     */
    private $packer = null;

    /**
     * Packer constructor.
     */
    public function __construct()
    {
        if (false && function_exists('thrift_protocol_read_binary') && function_exists('thrift_protocol_write_binary'))
        {
            $this->packer = new Extension();
        }
        else
        {
            $this->packer = new Native();
        }
    }

    /**
     * @param $successStruct
     * @param $exceptionStruct
     * @param $successData
     * @param $exceptionData
     * @return array
     */
    public function struct($successStruct, $exceptionStruct, $successData = null, $exceptionData = null)
    {
        // init success struct
        $successStruct['var'] = $this->successKey;
        $successStruct['value'] = $successData;

        // init exception struct
        foreach ($exceptionStruct as $eK => $eSpec)
        {
            if (is_object($exceptionData) && $eSpec['class'] === '\\'.get_class($exceptionData))
            {
                $exceptionStruct[$eK]['value'] = $exceptionData;
            }
            else
            {
                $exceptionStruct[$eK]['value'] = null;
            }
        }

        // merge struct
        return array_merge([$successStruct], $exceptionStruct);
    }

    /**
     * @param $type
     * @param $name
     * @param $args
     * @return string
     */
    public function encode($type, $name, $args)
    {
        return $this->packer->encode($type, $name, $args);
    }

    /**
     * @param $data
     * @param $args
     * @return array
     * @throws SysException
     */
    public function decode($data, $args)
    {
        return $this->packer->decode($data, $args);
    }
}