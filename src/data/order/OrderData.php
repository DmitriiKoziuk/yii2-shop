<?php
namespace DmitriiKoziuk\yii2Shop\data\order;

use DmitriiKoziuk\yii2Shop\data\CartData;
use DmitriiKoziuk\yii2Shop\data\CustomerData;
use DmitriiKoziuk\yii2Shop\entities\Order;

class OrderData
{
    /**
     * @var array
     */
    private $_order;

    /**
     * @var CartData
     */
    private $_cartData;

    /**
     * @var CustomerData
     */
    private $_customerData;

    /**
     * @var OrderStageLogData
     */
    private $_orderStageLogData;

    public function __construct(
        Order $order,
        CartData $cartData,
        CustomerData $customerData,
        OrderStageLogData $orderStageLogData
    ) {
        $this->_order = $order;
        $this->_cartData = $cartData;
        $this->_customerData = $customerData;
        $this->_orderStageLogData = $orderStageLogData;
    }

    public function getId(): int
    {
        return $this->_order->id;
    }

    public function customer(): CustomerData
    {
        return $this->_customerData;
    }

    public function cart(): CartData
    {
        return $this->_cartData;
    }

    public function stageLog()
    {
        return $this->_orderStageLogData;
    }
}