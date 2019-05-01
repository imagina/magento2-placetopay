<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client\Rest\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use \Imagina\Placetopay\Model\Client\Rest\Order;

class Processor
{
    /**
     * @var \Imagina\Placetopay\Model\Order\Processor
     */
    protected $orderProcessor;

    public function __construct(
        \Imagina\Placetopay\Model\Order\Processor $orderProcessor
    ) {
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @param string $placetopayOrderId
     * @param string $status
     * @param float $amount
     * @param bool $newest
     * @return bool
     * @throws LocalizedException
     */
    public function processStatusChange($placetopayOrderId, $status = '', $amount = null, $newest = true)
    {
        if (!in_array($status, [
            Order::STATUS_NEW,
            Order::STATUS_PENDING,
            Order::STATUS_CANCELLED,
            Order::STATUS_REJECTED,
            Order::STATUS_WAITING,
            Order::STATUS_COMPLETED
        ])) {
            throw new LocalizedException(new Phrase('Invalid status.'));
        }
        if (!$newest) {
            $close = in_array($status, [
                Order::STATUS_CANCELLED,
                Order::STATUS_REJECTED,
                Order::STATUS_COMPLETED
            ]);
            $this->orderProcessor->processOld($placetopayOrderId, $status, $close);
            return true;
        }
        switch ($status) {
            case Order::STATUS_NEW:
            case Order::STATUS_PENDING:
                $this->orderProcessor->processPending($placetopayOrderId, $status);
                return true;
            case Order::STATUS_CANCELLED:
            case Order::STATUS_REJECTED:
                $this->orderProcessor->processHolded($placetopayOrderId, $status);
                return true;
            case Order::STATUS_WAITING:
                $this->orderProcessor->processWaiting($placetopayOrderId, $status);
                return true;
            case Order::STATUS_COMPLETED:
                $this->orderProcessor->processCompleted($placetopayOrderId, $status, $amount);
                return true;
        }
    }
}
