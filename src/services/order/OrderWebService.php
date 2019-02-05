<?php
namespace DmitriiKoziuk\yii2Shop\services\order;

use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Shop\data\order\OrderData;
use DmitriiKoziuk\yii2Shop\data\order\OrdersData;
use DmitriiKoziuk\yii2Shop\data\order\OrderSearchParams;
use DmitriiKoziuk\yii2Shop\entities\Order;
use DmitriiKoziuk\yii2Shop\repositories\OrderRepository;
use DmitriiKoziuk\yii2Shop\services\cart\CartWebService;
use DmitriiKoziuk\yii2Shop\services\customer\CustomerWebService;

final class OrderWebService
{
    /**
     * @var OrderSearchService
     */
    private $_orderSearchService;

    /**
     * @var CartWebService
     */
    private $_cartWebService;

    /**
     * @var CustomerWebService
     */
    private $_customerWebService;

    /**
     * @var OrderRepository
     */
    private $_orderRepository;

    /**
     * @var OrderStageLogService
     */
    private $_orderStageLogService;

    public function __construct(
        OrderSearchService $orderSearchService,
        CartWebService $cartWebService,
        CustomerWebService $customerWebService,
        OrderRepository $orderRepository,
        OrderStageLogService $orderStageLogService
    ) {
        $this->_orderSearchService = $orderSearchService;
        $this->_cartWebService = $cartWebService;
        $this->_customerWebService = $customerWebService;
        $this->_orderRepository = $orderRepository;
        $this->_orderStageLogService = $orderStageLogService;
    }

    public function searchOrders(OrderSearchParams $orderSearchParams): OrdersData
    {
        $orders = [];
        $activeDataProvider = $this->_orderSearchService->searchOrdersBy($orderSearchParams);
        /** @var Order[] $models */
        $models = $activeDataProvider->getModels();
        foreach ($models as $model) {
            $orders[] = $this->_getOrderData($model);
        }
        return new OrdersData($activeDataProvider, $orders);
    }

    public function getOrderById(int $id): OrderData
    {
        $order = $this->_orderRepository->getOrderById($id);
        if (empty($order)) {
            throw new EntityNotFoundException("Order with id '{$id}' not exist.");
        }
        return $this->_getOrderData($order);
    }

    private function _getOrderData(Order $order): OrderData
    {
        $cartData = $this->_cartWebService->getCartById($order->id);
        $customerData = $this->_customerWebService->getCustomerById($cartData->getCustomerId());
        $orderStageLog = $this->_orderStageLogService->getOrderLog($order->id);
        return new OrderData($order, $cartData, $customerData, $orderStageLog);
    }
}