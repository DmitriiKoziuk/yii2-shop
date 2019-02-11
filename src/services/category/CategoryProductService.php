<?php
namespace DmitriiKoziuk\yii2Shop\services\category;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryProductRepository;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\entities\CategoryProduct;

final class CategoryProductService extends DBActionService
{
    /**
     * @var CategoryRepository
     */
    private $_categoryRepository;

    /**
     * @var CategoryProductRepository
     */
    private $_categoryProductRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryProductRepository $categoryProductRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryProductRepository = $categoryProductRepository;
    }

    /**
     * @param int $productId
     * @param int|null $newCategoryId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function updateRelation(int $productId, int $newCategoryId = null): void
    {
        $this->_deleteRelations($productId);
        if (! empty($newCategoryId)) {
            $category = $this->_categoryRepository->getById($newCategoryId);
            $this->_createRelations($category, $productId);
        }
    }

    /**
     * @param Category $category
     * @param int $productId
     * @throws \Throwable
     */
    private function _createRelations(Category $category, int $productId): void
    {
        try {
            $this->_createRelation($category, $productId);
            foreach ($category->parentList as $parentCategory) {
                $this->_createRelation($parentCategory, $productId);
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param Category $category
     * @param int $productId
     * @throws \Throwable
     */
    private function _createRelation(Category $category, int $productId): void
    {
        try {
            $relation = new CategoryProduct();
            $relation->category_id = $category->id;
            $relation->product_id = $productId;
            $relation->sort = $this->_defineSort($category);
            $this->_categoryProductRepository->save($relation);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param int $productId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function _deleteRelations(int $productId): void
    {
        $allRelations = $this->_categoryProductRepository->getAllProductRelations($productId);
        foreach ($allRelations as $relation) {
            $this->_deleteRelation($relation);
        }
    }

    /**
     * @param CategoryProduct $categoryProduct
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function _deleteRelation(CategoryProduct $categoryProduct): void
    {
        $categoryProduct->delete();
        CategoryProduct::updateAllCounters(
            ['sort' => -1],
            "category_id = {$categoryProduct->category_id} AND sort > {$categoryProduct->sort}"
        );
    }

    private function _defineSort(Category $category): int
    {
        $count = (int) CategoryProduct::find()
            ->where(['category_id' => $category->id])
            ->count();
        return ++$count;
    }
}