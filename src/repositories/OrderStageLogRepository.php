<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2Shop\entities\OrderStageLog;

class OrderStageLogRepository extends EntityRepository
{
    /**
     * @param int $orderId
     * @return OrderStageLog[]
     */
    public function getLog(int $orderId): array
    {
        /** @var OrderStageLog[] $log */
        $log = OrderStageLog::find()
            ->where(['order_id' => $orderId])
            ->all();
        return $log;
    }
}