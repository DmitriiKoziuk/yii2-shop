<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;

class ProductBrandWidget extends Widget
{
    public $brands;

    public $indexPageUrl;

    public function run()
    {
        if (! empty($this->brands)) {
            return $this->render('product-brand', [
                'brands' => $this->brands,
                'indexPageUrl' => $this->indexPageUrl,
            ]);
        }
        return '';
    }
}
