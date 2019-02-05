<?php
namespace DmitriiKoziuk\yii2Shop\services\product;

use yii\db\Connection;
use yii\helpers\Inflector;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\repositories\ProductTypeRepository;
use DmitriiKoziuk\yii2Shop\forms\product\ProductTypeInputForm;

class ProductTypeService extends EntityActionService
{
    private $_productTypeRepository;

    public function __construct(
        ProductTypeRepository $productTypeRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_productTypeRepository = $productTypeRepository;
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
    ): ProductType {   //TODO create event when change product url prefix.
        try {
            if ($productType->isNewRecord) {
                throw new \Exception('Save product type before update.');
            }
            $productType->setAttributes($productTypeInputForm->getAttributes());
            if ($productType->isAttributeChanged('name')) {
                $productType->code = Inflector::slug($productType->name);
            }
            $this->_productTypeRepository->save($productType);
            return $productType;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function delete(ProductType $productType)
    {
        //TODO make delete method.
    }
}