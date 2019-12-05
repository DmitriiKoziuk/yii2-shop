<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\entityViews\ProductEntityView;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuEntityView;

class ProductPreviewAttributesWidget extends Widget
{
    /**
     * @var ProductEntityView|ProductSkuEntityView
     */
    public $product;

    public function run()
    {
        return $this->render('product-preview-attributes', [
            'product' => $this->product,
        ]);
    }
}
