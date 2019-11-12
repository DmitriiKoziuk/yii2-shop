<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\backend;

use yii\base\Widget;
use yii\db\Expression;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity;

class ProductSkuUpdateAttributesWidget extends Widget
{
    /** @var ProductSku */
    public $productSkuId;

    /** @var ProductSku */
    private $productSku;

    /** @var EavAttributeEntity[] */
    private $productTypeAttributes;

    private $productSkuEavValues = [];

    public function init()
    {
        parent::init();
        $this->productSku = ProductSku::find()
            ->with([
                'eavVarcharValues.eavAttribute',
                'eavTextValues.eavAttribute',
                'eavDoubleValues.eavAttribute',
            ])
            ->where(['id' => $this->productSkuId])
            ->one();
        $this->productTypeAttributes = EavAttributeEntity::find()
            ->innerJoin(
                ProductTypeAttributeEntity::tableName(),
                [
                    ProductTypeAttributeEntity::tableName() . '.attribute_id' => new Expression(EavAttributeEntity::tableName() . '.id'),
                ]
            )->andWhere([
                ProductTypeAttributeEntity::tableName() . '.product_type_id' => $this->productSku->getTypeID(),
            ])->all();
        foreach ($this->productSku->eavVarcharValues as $value) {
            $this->productSkuEavValues[ $value->attribute_id ][] = $value;
        }
        foreach ($this->productSku->eavTextValues as $value) {
            $this->productSkuEavValues[ $value->attribute_id ][] = $value;
        }
        foreach ($this->productSku->eavDoubleValues as $value) {
            $this->productSkuEavValues[ $value->attribute_id ][] = $value;
        }
    }

    public function run()
    {
        return $this->render('product-sku-update-attributes', [
            'productSkuId' => $this->productSku->id,
            'attributes' => $this->productTypeAttributes,
            'attributeValues' => $this->productSkuEavValues,
        ]);
    }
}