<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\tests\_fixtures;

use yii\test\ActiveFixture;
use DmitriiKoziuk\yii2Shop\entities\Category;

class CategoryFixture extends ActiveFixture
{
    public $modelClass = Category::class;
}