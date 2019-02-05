<?php
namespace DmitriiKoziuk\yii2Shop\services\order;

use DmitriiKoziuk\yii2Shop\data\order\OrderStageLogData;
use DmitriiKoziuk\yii2Shop\repositories\OrderStageLogRepository;

final class OrderStageLogService
{
    private $_orderStageLogRepository;

    public function __construct(
        OrderStageLogRepository $orderStageLogRepository
    ) {
        $this->_orderStageLogRepository = $orderStageLogRepository;
    }

    public function getOrderLog(int $orderId)
    {
        $log = $this->_orderStageLogRepository->getLog($orderId);
        if (empty($log)) {
            $log = [];
        }
        return new OrderStageLogData($log);
    }
}