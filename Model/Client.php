<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Client
{

    /**
     * @var Client\ConfigInterface
     */
    protected $configHelper;



    /**
     * @var Client\OrderInterface
     */
    protected $orderHelper;


    /**
     * @param Client\ConfigInterface $configHelper
     * @param Client\OrderInterface $orderHelper
     */
    public function __construct(
        Client\ConfigInterface $configHelper,
        Client\OrderInterface $orderHelper

    ) {
        $this->orderHelper = $orderHelper;
        $this->configHelper = $configHelper;
        $configHelper->setConfig();
    }

    /**
     * @param array $data
     * @return array (keys: orderId, redirectUri, extOrderId)
     * @throws LocalizedException
     */
    public function orderCreate(array $data = [])
    {
        if (!$this->orderHelper->validateCreate($data)) {
            throw new LocalizedException(new Phrase('Order request data array is invalid.'));
        }
        $data = $this->orderHelper->addSpecialDataToOrder($data);

        $result = $this->orderHelper->create($data);
        if (!$result) {
            throw new LocalizedException(new Phrase('There was a problem while processing order create request.'));
        }
        return $result;
    }

    /**
     * @param string $placetopayOrderId
     * @return string Transaction status
     * @throws LocalizedException
     */
    public function orderRetrieve($placetopayOrderId)
    {
        if (!$this->orderHelper->validateRetrieve($placetopayOrderId)) {
            throw new LocalizedException(new Phrase('ID of order to retrieve is empty.'));
        }
        $result = $this->orderHelper->retrieve($placetopayOrderId);
        if (!$result) {
            throw new LocalizedException(new Phrase('There was a problem while processing order retrieve request.'));
        }
        return $result;
    }

    /**
     * @param string $placetopayOrderId
     * @return bool|\OpenPayU_Result
     * @throws LocalizedException
     */
    public function orderCancel($placetopayOrderId)
    {
        if (!$this->orderHelper->validateCancel($placetopayOrderId)) {
            throw new LocalizedException(new Phrase('ID of order to cancel is empty.'));
        }
        $result = $this->orderHelper->cancel($placetopayOrderId);
        if (!$result) {
            throw new LocalizedException(new Phrase('There was a problem while processing order cancel request.'));
        }
        return $result;
    }

    /**
     * @param array $data
     * @return true
     * @throws LocalizedException
     */
    public function orderStatusUpdate(array $data = [])
    {
        if (!$this->orderHelper->validateStatusUpdate($data)) {
            throw new LocalizedException(new Phrase('Order status update request data array is invalid.'));
        }
        $result = $this->orderHelper->statusUpdate($data);
        if (!$result) {
            throw new LocalizedException(
                new Phrase('There was a problem while processing order status update request.')
            );
        }
        return true;
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @return array (keys: placetopayOrderId, status, amount)
     * @throws LocalizedException
     */
    public function orderConsumeNotification(\Magento\Framework\App\Request\Http $request)
    {
        $result = $this->orderHelper->consumeNotification($request);
        if (!$result) {
            throw new LocalizedException(new Phrase('There was a problem while consuming order notification.'));
        }
        return $result;
    }


    /**
     * @return Client\OrderInterface
     */
    public function getOrderHelper()
    {
        return $this->orderHelper;
    }

    /**
     * @return Client\ConfigInterface
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

}
