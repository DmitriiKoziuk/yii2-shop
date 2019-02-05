<?php
namespace DmitriiKoziuk\yii2Shop\data\order;

use DmitriiKoziuk\yii2Shop\entities\OrderStageLog;

final class OrderStageLogData
{
    /**
     * @var OrderStageLog[]
     */
    private $_orderLog;

    /**
     * @var int
     */
    private $_orderTime;

    /**
     * OrderStageLogData constructor.
     * @param OrderStageLog[] $orderLog
     */
    public function __construct(array $orderLog)
    {
        $this->_orderLog = $orderLog;
        $this->initLocalProperties();
    }

    public function initLocalProperties()
    {
        foreach ($this->_orderLog as $log) {
            if ($log->stage_id = OrderStage::ORDER_CREATED) {
                $this->_orderTime = $log->created_at;
            }
        }
    }

    public function getOrderTime($format = 'Y-m-d H:i:s'): string
    {
        return date($format, $this->_orderTime);
    }
}