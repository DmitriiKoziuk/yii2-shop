<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;

class ProductSearchWidget extends Widget
{
    public function run()
    {
        return $this->render('product-search');
    }
}