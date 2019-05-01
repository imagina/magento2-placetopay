<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Processor
{
    /**
     * @var \Imagina\Placetopay\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Imagina\Placetopay\Model\Transaction\Service
     */
    protected $transactionService;

    /**
     * @param \Imagina\Placetopay\Model\Order $orderHelper
     * @param \Imagina\Placetopay\Model\Transaction\Service $transactionService
     */
    public function __construct(
        \Imagina\Placetopay\Model\Order $orderHelper,
        \Imagina\Placetopay\Model\Transaction\Service $transactionService
    ) {
        $this->orderHelper = $orderHelper;
        $this->transactionService = $transactionService;
    }

    /**
     * @param string $placetopayOrderId
     * @param string$status
     * @param bool $close
     * @throws LocalizedException
     */
    public function processOld($placetopayOrderId, $status, $close = false)
    {
        $this->transactionService->updateStatus($placetopayOrderId, $status, $close);
    }

    /**
     * @param string $placetopayOrderId
     * @param string $status
     * @throws LocalizedException
     */
    public function processPending($placetopayOrderId, $status)
    {
        $this->transactionService->updateStatus($placetopayOrderId, $status);
    }

    /**
     * @param string $placetopayOrderId
     * @param string $status
     * @throws LocalizedException
     */
    public function processHolded($placetopayOrderId, $status)
    {
        $order = $this->loadOrderByPlacetpplOrderId($placetopayOrderId);
        $this->orderHelper->setHoldedOrderStatus($order, $status);
        $this->transactionService->updateStatus($placetopayOrderId, $status, true);
    }

    /**
     * @param string $placetopayOrderId
     * @param string $status
     * @throws LocalizedException
     * @todo Implement some additional logic for transaction confirmation by store owner.
     */
    public function processWaiting($placetopayOrderId, $status)
    {
        $this->transactionService->updateStatus($placetopayOrderId, $status);
    }

    /**
     * @param string $placetopayOrderId
     * @param string $status
     * @param float $amount
     * @throws LocalizedException
     */
    public function processCompleted($placetopayOrderId, $status, $amount)
    {
        $order = $this->loadOrderByPlacetpplOrderId($placetopayOrderId);
        $this->orderHelper->completePayment($order, $amount, $placetopayOrderId);
        $this->transactionService->updateStatus($placetopayOrderId, $status, true);
    }

    /**
     * @param string $placetopayOrderId
     * @return \Imagina\Placetopay\Model\Sales\Order
     * @throws LocalizedException
     */
    protected function loadOrderByPlacetpplOrderId($placetopayOrderId)
    {
        $order = $this->orderHelper->loadOrderByPlacetpplOrderId($placetopayOrderId);
        if (!$order) {
            throw new LocalizedException(new Phrase('Order not found.'));
        }
        return $order;
    }
}
