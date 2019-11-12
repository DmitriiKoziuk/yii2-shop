<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

class ProductSkuViewAttributesWidget extends Widget
{
    /** @var ProductSku */
    public $productSkuId;

    /** @var ProductSku */
    private $productSku;

    /** @var EavAttributeEntity[] */
    private $productAttributes;

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
        $this->productAttributes = [];
        foreach ($this->productSku->eavVarcharValues as $value) {
            $this->productAttributes[ $value->attribute_id ] = $value->eavAttribute;
            $this->productSkuEavValues[ $value->attribute_id ][] = $value;
        }
        foreach ($this->productSku->eavTextValues as $value) {
            $this->productAttributes[ $value->attribute_id ] = $value->eavAttribute;
            $this->productSkuEavValues[ $value->attribute_id ][] = $value;
        }
        foreach ($this->productSku->eavDoubleValues as $value) {
            $this->productAttributes[ $value->attribute_id ] = $value->eavAttribute;
            $this->productSkuEavValues[ $value->attribute_id ][] = $value;
        }
    }

    public function run()
    {
        return $this->render('product-sku-view-attributes', [
            'productSkuId' => $this->productSku->id,
            'attributes' => $this->productAttributes,
            'attributeValues' => $this->productSkuEavValues,
        ]);
    }
}