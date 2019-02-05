<?php
namespace DmitriiKoziuk\yii2Shop\services\category;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\entities\CategoryProductSku;

final class CategoryProductSkuService extends EntityActionService
{
    /**
     * @var CategoryRepository
     */
    private $_categoryRepository;

    /**
     * @var CategoryProductSkuRepository
     */
    private $_categoryProductSkuRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryProductSkuRepository $categoryProductSkuRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryProductSkuRepository = $categoryProductSkuRepository;
    }

    /**
     * @param int $productSkuId
     * @param int|null $newCategoryId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function updateRelation(int $productSkuId, int $newCategoryId = null): void
    {
        $this->_deleteRelations($productSkuId);
        if (! empty($newCategoryId)) {
            $category = $this->_categoryRepository->getById($newCategoryId);
            $this->_createRelations($category, $productSkuId);
        }
    }

    /**
     * @param Category $category
     * @param int $productSkuId
     * @throws \Throwable
     */
    private function _createRelations(Category $category, int $productSkuId): void
    {
        try {
            $this->_createRelation($category, $productSkuId);
            foreach ($category->parentList as $parentCategory) {
                $this->_createRelation($parentCategory, $productSkuId);
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param Category $category
     * @param int $productSkuId
     * @throws \Throwable
     */
    private function _createRelation(Category $category, int $productSkuId): void
    {
        try {
            $relation = new CategoryProductSku();
            $relation->category_id = $category->id;
            $relation->product_sku_id = $productSkuId;
            $relation->sort = $this->_defineSort($category);
            $this->_categoryProductSkuRepository->save($relation);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param int $productSkuId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function _deleteRelations(int $productSkuId): void
    {
        $allRelations = $this->_categoryProductSkuRepository->getAllProductRelations($productSkuId);
        foreach ($allRelations as $relation) {
            $this->_deleteRelation($relation);
        }
    }

    /**
     * @param CategoryProductSku $categoryProductSku
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function _deleteRelation(CategoryProductSku $categoryProductSku): void
    {
        $categoryProductSku->delete();
        CategoryProductSku::updateAllCounters(
            ['sort' => -1],
            "category_id = {$categoryProductSku->category_id} AND sort > {$categoryProductSku->sort}"
        );
    }

    private function _defineSort(Category $category): int
    {
        $count = (int) CategoryProductSku::find()
            ->where(['category_id' => $category->id])
            ->count();
        return ++$count;
    }
}