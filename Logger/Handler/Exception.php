<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Logger\Handler;

use Monolog\Logger;

class Exception extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/imagina/placetopay/exception.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::CRITICAL;
}
