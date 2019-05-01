<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Block\Payment;

class Info extends \Magento\Payment\Block\Info
{
    /**
     * @var \Imagina\Placetopay\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var \Imagina\Placetopay\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Imagina\Placetopay\Model\ResourceModel\Transaction $transactionResource
     * @param \Imagina\Placetopay\Model\ClientFactory $clientFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Imagina\Placetopay\Model\ResourceModel\Transaction $transactionResource,
        \Imagina\Placetopay\Model\ClientFactory $clientFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->transactionResource = $transactionResource;
        $this->clientFactory = $clientFactory;
    }

    protected function _prepareLayout()
    {
        $this->addChild('buttons', Info\Buttons::class);
        parent::_prepareLayout();
    }

    protected function _prepareSpecificInformation($transport = null)
    {
        /**
         * @var $client \Imagina\Placetopay\Model\Client
         */
        $transport = parent::_prepareSpecificInformation($transport);
        $orderId = $this->getInfo()->getParentId();
        $status = $this->transactionResource->getLastStatusByOrderId($orderId);
        $client = $this->clientFactory->create();
        $statusDescription = $client->getOrderHelper()->getStatusDescription($status);
        $transport->setData((string) __('Status'), $statusDescription);
        return $transport;
    }
}
