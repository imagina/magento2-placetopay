<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client\Classic;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Imagina\Placetopay\Model\Client\ConfigInterface;
use Imagina\Placetopay\Model\Placetopay;

class Config implements ConfigInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $seed;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * @var string
     */
    protected $tranKey;

    /**
     * @var string
     */
    protected $test;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return true
     * @throws LocalizedException
     */
    public function setConfig()
    {


        $merchantId = $this->scopeConfig->getValue(Placetopay::XML_PATH_MERCHANT_ID, 'store');
        if ($merchantId) {
            $this->merchantId = $merchantId;
        } else {
            throw new LocalizedException(new Phrase('merchantId is empty.'));
        }

        $accountId = $this->scopeConfig->getValue(Placetopay::XML_PATH_ACCOUNT_ID, 'store');
        if ($accountId) {
            $this->accountId = $accountId;
        } else {
            throw new LocalizedException(new Phrase('accountId is empty.'));
        }

        $ApiKey = $this->scopeConfig->getValue(Placetopay::XML_PATH_API_KEY, 'store');
        if ($ApiKey) {
            $this->ApiKey = $ApiKey;
        } else {
            throw new LocalizedException(new Phrase('ApiKey is empty.'));
        }

        $ApiLogin = $this->scopeConfig->getValue(Placetopay::XML_PATH_API_LOGIN, 'store');
        if ($ApiLogin) {
            $this->ApiLogin = $ApiLogin;
        } else {
            throw new LocalizedException(new Phrase('ApiLogin is empty.'));
        }


        if ($this->scopeConfig->isSetFlag(Placetopay::XML_PATH_TEST, 'store')) {
            $this->test = 1;
            $this->url = 'https://sandbox.gateway.placetopay.com/ppp-web-gateway/';
        } else {
            $this->test = 0;
            $this->url = 'https://gateway.placetopay.com/ppp-web-gateway/';
        }


        return true;
    }

    /**
     * @param string|null $key
     * @return string|array
     */
    public function getConfig($key = null)
    {
        $config = [
            'login' => $this->login,
            'seed' => $this->seed,
            'secretKey' => $this->secretKey,
            'test' => $this->test,
            'url' => $this->url,
        ];
        if ($key) {
            return $config[$key];
        }
        return $config;
    }
}
