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
    protected $email;

     /**
     * @var string
     */
    protected $phone;

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
    protected $mode;

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
        /*
        if ($this->scopeConfig->isSetFlag(Placetopay::XML_PATH_TEST, 'store')) {
            $this->test = 1;
            $this->url = 'https://test.placetopay.com/soap/pse/?wsdl'; // URL TESTING
        } else {
            $this->test = 0;
            $this->url = 'https://test.placetopay.com/soap/pse/?wsdl'; // URL PRODUCTION
        }
        */
        
        $mode = $this->scopeConfig->getValue(Placetopay::XML_PATH_MODE, 'store');

        if ($mode) {

            if($mode=="development"){
                $this->test = 0;
                $this->url = 'https://dev.placetopay.com/redirection/';
            }

            if($mode=="testing"){
                $this->test = 1;
                $this->url = 'https://test.placetopay.com/redirection/';
            }

            if($mode=="production"){
                $this->test = 2;
                $this->url = 'https://secure.placetopay.com/redirection/';
            }
            
        } else {
           
            $this->mode = "development";
            $this->test = 0;
            $this->url = 'https://dev.placetopay.com/redirection/';
        }

        $login = $this->scopeConfig->getValue(Placetopay::XML_PATH_LOGIN, 'store');
        if ($login) {
            $this->login = $login;
        } else {
            throw new LocalizedException(new Phrase('login is empty.'));
        }

        $secretKey = $this->scopeConfig->getValue(Placetopay::XML_PATH_SECRET_KEY, 'store');
        if ($secretKey) {
            $this->secretKey = $secretKey;
        } else {
            throw new LocalizedException(new Phrase('secret key is empty.'));
        }

        $email = $this->scopeConfig->getValue(Placetopay::XML_PATH_EMAIL, 'store');
        if ($email) {
            $this->email = $email;
        } else {
            $this->email = "email@email.com";
        }

        $phone = $this->scopeConfig->getValue(Placetopay::XML_PATH_PHONE, 'store');
        if ($phone) {
            $this->phone = $phone;
        } else {
            $this->phone = "3XXXXXXX";
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
            'secretKey' => $this->secretKey,
            'mode' => $this->mode,
            'url' => $this->url,
            'email' => $this->email,
            'phone' => $this->phone
        ];
        if ($key) {
            return $config[$key];
        }
        return $config;
    }
}
