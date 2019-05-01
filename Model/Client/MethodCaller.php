<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client;

use Imagina\Placetopay\Model\Client\MethodCallerInterface;

class MethodCaller implements MethodCallerInterface
{
    /**
     * @var MethodCaller\RawInterface
     */
    protected $_rawMethod;

    /**
     * @var \Imagina\Placetopay\Logger\Logger
     */
    protected $_logger;

    /**
     * @param MethodCaller\RawInterface $rawMethod
     * @param \Imagina\Placetopay\Logger\Logger $logger
     */
    public function __construct(
        MethodCaller\RawInterface $rawMethod,
        \Imagina\Placetopay\Logger\Logger $logger
    ) {
        $this->_rawMethod = $rawMethod;
        $this->_logger = $logger;
    }

    /**
     * @param string $methodName
     * @param array $args
     * @return \stdClass|false
     */
    public function call($methodName, array $args = [])
    {
        try {
            return $this->_rawMethod->call($methodName, $args);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return false;
        }
    }
}
