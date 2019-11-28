<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\data\CategoryData;

class SubcategoriesWidget extends Widget
{
    /**
     * @var CategoryData
     */
    public $category;

    public function run()
    {
        return $this->render('subcategories', [
            'category' => $this->category,
        ]);
    }
}
