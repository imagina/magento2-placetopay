<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client;

class Rest extends \Imagina\Placetopay\Model\Client
{
    /**
     * @param Rest\Config $configHelper
     * @param Rest\Order $orderHelper
     */
    public function __construct(
        Rest\Config $configHelper,
        Rest\Order $orderHelper
    ) {
        parent::__construct(
            $configHelper,
            $orderHelper
        );
    }
}
