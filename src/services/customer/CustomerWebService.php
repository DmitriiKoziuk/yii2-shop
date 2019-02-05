<?php
namespace DmitriiKoziuk\yii2Shop\services\customer;

use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Shop\data\CustomerData;
use DmitriiKoziuk\yii2Shop\repositories\CustomerRepository;

class CustomerWebService
{
    private $_customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->_customerRepository = $customerRepository;
    }

    public function getCustomerById(int $id): CustomerData
    {
        $customer = $this->_customerRepository->getById($id);
        if (empty($customer)) {
            throw new EntityNotFoundException("Customer with id '{$id}' not exist.");
        }
        return new CustomerData($customer);
    }
}