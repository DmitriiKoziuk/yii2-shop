<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\Currency;

class CurrencyData
{
    private $_currencyRecord;

    public function __construct(Currency $currencyRecord)
    {
        $this->_currencyRecord = $currencyRecord;
    }

    public function getId()
    {
        return $this->_currencyRecord->id;
    }

    public function getName()
    {
        return $this->_currencyRecord->name;
    }

    public function getCode()
    {
        return $this->_currencyRecord->code;
    }

    public function getRate()
    {
        return $this->_currencyRecord->rate;
    }

    public function __get($name)
    {
        return $this->{'get' . $name}();
    }
}