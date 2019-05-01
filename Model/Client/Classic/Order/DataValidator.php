<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client\Classic\Order;

class DataValidator extends \Imagina\Placetopay\Model\Client\DataValidator
{
    /**
     * @var array
     */
    protected $requiredBasicKeys = [
        'amount',
        'description',
        'buyerFullName',
        'buyerEmail',
        'referenceCode',
        'currency'
    ];

    /**
     * @param array $data
     * @return bool
     */
    public function validateBasicData(array $data = [])
    {
        foreach ($this->getRequiredBasicKeys() as $key) {
            if (!isset($data[$key]) || empty($data[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array
     */
    protected function getRequiredBasicKeys()
    {
        return $this->requiredBasicKeys;
    }
}
