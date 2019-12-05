<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\entityViews\ProductEntityView;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;

class ProductPreviewAttributesWidget extends Widget
{
    /**
     * @var ProductEntityView|ProductSkuView
     */
    public $product;

    public function run()
    {
        return $this->render('product-preview-attributes', [
            'product' => $this->product,
        ]);
    }
}
