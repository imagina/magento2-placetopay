<?php
namespace Imagina\Placetopay\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class Placetopay  extends AbstractMethod
{
    const CODE = 'placetopay';

    /*
     * Path of config variables in system.xml
     */
    const XML_PATH_LOGIN               = 'payment/placetopay/login';
    const XML_PATH_SECRET_KEY           = 'payment/placetopay/secretKey';
    const XML_PATH_TEST       = 'payment/placetopay/test';
    const XML_PATH_MODE       = 'payment/placetopay/mode';


    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_stripeApi = false;

    /**
     * @var string
    */
    protected $_code = self::CODE;

    protected $_supportedCurrencyCodes = array('COP','USD');

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data
        );

        $this->urlBuilder = $urlBuilder;

    }


    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }



}