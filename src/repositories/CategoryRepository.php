<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Category;

class CategoryRepository extends AbstractActiveRecordRepository
{
    public function getById(int $categoryId): ?Category
    {
        /** @var Category|null $category */
        $category = Category::find()->where(['id' => $categoryId])->one();
        return $category;
    }

    public function getParentCategory(Category $category): Category
    {
        /** @var Category $parentCategory */
        $parentCategory = Category::find()
            ->where(['id' => $category->parent_id])
            ->one();
        return $parentCategory;
    }
}