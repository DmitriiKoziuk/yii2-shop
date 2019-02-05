<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\Customer;

class CustomerData
{
    private $_customer;

    public function __construct(Customer $customer)
    {
        $this->_customer = $customer;
    }

    public function getFirstName(): string
    {
        return $this->_customer->first_name;
    }

    public function getLastName(): string
    {
        return $this->_customer->last_name;
    }

    public function getPhoneNumber(): string
    {
        $phoneNumber = $this->_customer->phone_number;
        $digitsNumber = mb_strlen($phoneNumber);
        switch ($digitsNumber) {
            case 13:
                return preg_replace(
                    "/([+]{1})([0-9]{3})([0-9]{2})([0-9]{3})([0-9]{2})([0-9]{2})/",
                    "$1$2 ($3) $4-$5-$6",
                    $phoneNumber
                );
            break;
            case 10:
                return preg_replace(
                    "/([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/",
                    "($1) $2-$3-$4",
                    $phoneNumber
                );
            break;
            default:
                return $phoneNumber;
            break;
        }
    }
}