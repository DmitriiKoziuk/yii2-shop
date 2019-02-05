<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\Supplier;

class SupplierData
{
    /**
     * @var Supplier
     */
    private $_supplier;

    public function __construct(Supplier $supplier)
    {
        $this->_supplier = $supplier;
    }

    public function getId()
    {
        return $this->_supplier->id;
    }

    public function getName()
    {
        return $this->_supplier->name;
    }
}