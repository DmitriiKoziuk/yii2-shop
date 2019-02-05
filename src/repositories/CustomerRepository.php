<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2Shop\entities\Customer;

class CustomerRepository extends EntityRepository
{
    public function getByPhoneNumber(string $phoneNumber): ?Customer
    {
        /** @var Customer|null $customer */
        $customer = Customer::find()->where(['phone_number' => $phoneNumber])->one();
        return $customer;
    }

    public function getById(int $id): ?Customer
    {
        /** @var Customer|null $customer */
        $customer = Customer::find()->where(['id' => $id])->one();
        return $customer;
    }
}