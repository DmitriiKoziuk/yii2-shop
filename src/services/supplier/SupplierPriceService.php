<?php
namespace DmitriiKoziuk\yii2Shop\services\supplier;

use yii\db\Connection;
use yii\queue\cli\Queue;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Shop\data\SupplierPriceData;
use DmitriiKoziuk\yii2Shop\repositories\SupplierPriceRepository;
use DmitriiKoziuk\yii2Shop\jobs\ProcessSupplierPriceJob;

class SupplierPriceService extends DBActionService
{
    /**
     * @var SupplierPriceRepository
     */
    private $_supplierPriceRepository;

    /**
     * @var Queue
     */
    private $_queue;

    public function __construct(
        SupplierPriceRepository $supplierPriceRepository,
        Queue $queue,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_supplierPriceRepository = $supplierPriceRepository;
        $this->_queue = $queue;
    }

    public function getSupplierPriceById(int $id): ?SupplierPriceData
    {
        $supplierPriceRecord = $this->_supplierPriceRepository->getSupplierPriceById($id);
        if (empty($supplierPriceRecord)) {
            return null;
        }
        return new SupplierPriceData($supplierPriceRecord);
    }

    public function processSupplierPrice(int $supplierPriceId): void
    {
        $supplierPriceEntity = $this->_supplierPriceRepository
            ->getSupplierPriceById($supplierPriceId);
        $supplierPriceEntity->job_id = (string) $this->_queue->push(new ProcessSupplierPriceJob([
            'supplierPriceId' => $supplierPriceId,
        ]));
        $this->_supplierPriceRepository->save($supplierPriceEntity);
    }
}