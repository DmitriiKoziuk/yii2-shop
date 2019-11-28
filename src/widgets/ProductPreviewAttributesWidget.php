<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductData;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductSkuData;

class ProductPreviewAttributesWidget extends Widget
{
    /**
     * @var ProductData|ProductSkuData
     */
    public $product;

    public function run()
    {
        return $this->render('product-preview-attributes', [
            'product' => $this->product,
        ]);
    }
}
