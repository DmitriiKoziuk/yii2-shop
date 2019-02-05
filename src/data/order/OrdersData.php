<?php
namespace DmitriiKoziuk\yii2Shop\data\order;

use yii\data\DataProviderInterface;
use yii\data\Pagination;

class OrdersData implements DataProviderInterface
{
    /**
     * @var DataProviderInterface
     */
    private $_dataProvider;

    /**
     * @var OrderData[]
     */
    private $_orders;

    /**
     * OrdersData constructor.
     * @param DataProviderInterface $dataProvider
     * @param OrderData[] $orders
     */
    public function __construct(DataProviderInterface $dataProvider, array $orders)
    {
        $this->_dataProvider = $dataProvider;
        $this->_orders = $orders;
    }

    public function prepare($forcePrepare = false)
    {
    }

    public function getCount()
    {
        return $this->_dataProvider->getCount();
    }

    public function getTotalCount()
    {
        return $this->_dataProvider->getTotalCount();
    }

    /**
     * @return OrderData[]
     */
    public function getModels(): array
    {
        $models = [];
        foreach ($this->_orders as $order) {
            $models[] = $order;
        }
        return $models;
    }

    public function getKeys(): array
    {
        $keys = [];
        foreach ($this->_orders as $order) {
            $keys[] = $order->getId();
        }
        return $keys;
    }

    public function getSort(): bool
    {
        return false;
    }

    public function getPagination(): Pagination
    {
        return $this->_dataProvider->getPagination();
    }
}