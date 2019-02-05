<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2Shop\entities\Category;

final class CategoryRepository extends EntityRepository
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