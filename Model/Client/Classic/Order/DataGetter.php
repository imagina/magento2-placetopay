<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client\Classic\Order;

class DataGetter
{
    /**
     * @var \Imagina\Placetopay\Model\Order\ExtOrderId
     */
    protected $extOrderIdHelper;

    /**
     * @var \Imagina\Placetopay\Model\Client\Classic\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Imagina\Placetopay\Model\Session
     */
    protected $session;

    /**
     * @param \Imagina\Placetopay\Model\Order\ExtOrderId $extOrderIdHelper
     * @param \Imagina\Placetopay\Model\Client\Classic\Config $configHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Imagina\Placetopay\Model\Session $session
     */
    public function __construct(
        \Imagina\Placetopay\Model\Order\ExtOrderId $extOrderIdHelper,
        \Imagina\Placetopay\Model\Client\Classic\Config $configHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Imagina\Placetopay\Model\Session $session
    ) {
        $this->extOrderIdHelper = $extOrderIdHelper;
        $this->configHelper = $configHelper;
        $this->dateTime = $dateTime;
        $this->session = $session;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getBasicData(\Magento\Sales\Model\Order $order)
    {
        $incrementId = $order->getIncrementId();
        $billingAddress = $order->getBillingAddress();

        $taxReturnBase = number_format(($order->getGrandTotal() - $order->getTaxAmount()),2,'.','');
        if($order->getTaxAmount() == 0) $taxReturnBase = 0;

        $data = [
            'amount' => number_format($order->getGrandTotal(),2,'.',''),
            'description' => __('Order # %1', [$incrementId]) . " ",
            'extra1' => $incrementId,
            'extra2' => 'Imagina_Placetopay_M2',
            'buyerFullName' => $billingAddress->getFirstname(). ' '.$billingAddress->getLastname(),
            'buyerEmail' => $order->getCustomerEmail(),
            'referenceCode' => $this->extOrderIdHelper->generate($order),
            'currency' => $order->getOrderCurrencyCode(),
            'tax' => number_format($order->getTaxAmount(),2,'.',''),
            'taxReturnBase' => $taxReturnBase,
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->configHelper->getConfig('login');
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->configHelper->getConfig('secretKey');
    }

    /**
     * @return string
     */
    public function getTestMode()
    {
        return $this->configHelper->getConfig('test');
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return int
     */
    public function getTs()
    {
        return $this->dateTime->timestamp();
    }

    /**
     * @param array $data
     * @return string
     */
    public function getSigForOrderCreate(array $data = [])
    {
        //Signature Format
        //“ApiKey~merchantId~referenceCode~amount~currency”.
        /*
        return md5(
            $this->configHelper->getConfig('ApiKey')."~".
            $data['merchantId'] ."~".
            $data['referenceCode'] ."~".
            $data['amount']."~".
            $data['currency']
        );
        */

        return "123";
    }

    /**
     * @param array $data
     * @return string
     */
    public function getSigForOrderRetrieve(array $data = [])
    {
        return md5(
            $data['pos_id'] .
            $data['referenceCode'] .
            $data['ts'] .
            $this->configHelper->getConfig('key_md5')
        );
    }
}
