<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\product;

use yii\base\Event;
use yii\db\Connection;
use yii\queue\cli\Queue;
use yii\helpers\Inflector;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\data\ProductTypeData;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Shop\repositories\ProductTypeRepository;
use DmitriiKoziuk\yii2Shop\forms\product\ProductTypeInputForm;
use DmitriiKoziuk\yii2Shop\events\ProductTypeUpdateEvent;
use DmitriiKoziuk\yii2Shop\events\ProductTypeBeforeDeleteEvent;

class ProductTypeService extends DBActionService
{
    /**
     * @var ProductTypeRepository
     */
    private $_productTypeRepository;

    /**
     * @var Queue
     */
    private $_queue;

    public function __construct(
        ProductTypeRepository $productTypeRepository,
        Queue $queue,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_productTypeRepository = $productTypeRepository;
        $this->_queue = $queue;
    }

    /**
     * @param ProductTypeInputForm $productTypeInputForm
     * @return ProductType
     * @throws \Throwable
     */
    public function create(ProductTypeInputForm $productTypeInputForm): ProductType
    {
        try {
            $productType = new ProductType();
            $productType->setAttributes($productTypeInputForm->getAttributes());
            $productType->code = Inflector::slug($productType->name);
            $this->_productTypeRepository->save($productType);
            return $productType;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param ProductType $productType
     * @param ProductTypeInputForm $productTypeInputForm
     * @return ProductType
     * @throws \Throwable
     */
    public function update(
        ProductType $productType,
        ProductTypeInputForm $productTypeInputForm
    ): ProductType {
        try {
            if ($productType->isNewRecord) {
                throw new \Exception('Save product type before update.');
            }
            $productType->setAttributes($productTypeInputForm->getAttributes());
            if ($productType->isAttributeChanged('name')) {
                $productType->code = Inflector::slug($productType->name);
            }
            $changedAttributes = $productType->getDirtyAttributes();
            $this->_productTypeRepository->save($productType);
            Event::trigger(
                ProductTypeUpdateEvent::class,
                ProductTypeUpdateEvent::EVENT_PRODUCT_TYPE_UPDATE,
                new ProductTypeUpdateEvent([
                    'changedAttributes' => $changedAttributes,
                    'productTypeAttributes' => $productType->getAttributes(),
                ])
            );
            return $productType;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function deleteProductType(int $productTypeId): void
    {
        $productTypeEntity = $this->_productTypeRepository->getProductTypeById($productTypeId);
        $this->eventBeforeDelete($productTypeEntity->id);
        $this->_productTypeRepository->delete($productTypeEntity);
    }

    public function getProductTypeById(int $productTypeId): ProductTypeData
    {
        $productTypeRecord = $this->_productTypeRepository->getProductTypeById($productTypeId);
        return new ProductTypeData($productTypeRecord);
    }

    private function eventBeforeDelete(int $productTypeId): void
    {
        Event::trigger(
            ProductTypeBeforeDeleteEvent::class,
            ProductTypeBeforeDeleteEvent::EVENT_BEFORE_DELETE,
            new ProductTypeBeforeDeleteEvent([
                'productTypeId' => $productTypeId,
            ])
        );
    }
}
