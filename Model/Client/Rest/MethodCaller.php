<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client\Rest;

class MethodCaller extends \Imagina\Placetopay\Model\Client\MethodCaller
{
    public function __construct(
        MethodCaller\Raw $rawMethod,
        \Imagina\Placetopay\Logger\Logger $logger
    ) {
        parent::__construct(
            $rawMethod,
            $logger
        );
    }
}
