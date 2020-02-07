<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\repositories;

use Yii;
use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\CategoryProductSku;

class CategoryProductSkuRepository extends AbstractActiveRecordRepository
{
    /**
     * @param int $productSkuId
     * @return CategoryProductSku[]
     */
    public function getAllProductRelations(int $productSkuId): array
    {
        return CategoryProductSku::find()->where(['product_sku_id' => $productSkuId])->all();
    }

    public function getMaxSort(int $categoryId): int
    {
        return (int) CategoryProductSku::find()
            ->where(['category_id' => $categoryId])
            ->max('sort');
    }

    /**
     * @param int $categoryId
     * @param int $productSkuId
     * @param int $sort
     * @throws \Throwable
     */
    public function updateSort(int $categoryId, int $productSkuId, int $sort)
    {
        /** @var CategoryProductSku|null $categoryProductSkuEntity */
        $categoryProductSkuEntity = CategoryProductSku::find()
            ->where(['category_id' => $categoryId, 'product_sku_id' => $productSkuId])
            ->one();
        if (empty($categoryProductSkuEntity)) {
            throw new \Exception("Relation not exist for category id '{$categoryId}' and product sku id {$productSkuId}");
        }
        $maxSort = $this->getMaxSort($categoryId);
        $minSort = 1;
        if ($sort > $maxSort || $sort < $minSort) {
            throw new \Exception("Sort '{$sort}' out of range. Min. is {$minSort}, Max. is {$maxSort}.");
        }
        /**
         * Convert $sort
         * Use upside down sorting (SORT_DESC).
         * First position is $maxSort. Last position is $minSort.
         */
        if ($sort == $minSort) {
            $sort = $maxSort;
        } elseif ($sort == $maxSort) {
            $sort = $minSort;
        } else {
            $sort = ($maxSort + 1) - $sort;
        }
        if (
            $sort != $categoryProductSkuEntity->sort
        ) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->delete($categoryProductSkuEntity);
                $moveDownAllProductSkuWithHigherSortPosition = <<<Q
                    category_id = {$categoryId}
                    AND sort > {$categoryProductSkuEntity->sort}
                ORDER BY sort ASC
                Q;
                CategoryProductSku::updateAllCounters(
                    ['sort' => -1],
                    $moveDownAllProductSkuWithHigherSortPosition
                );
                $moveUpAllProductSkuWithHigherSortPosition = <<<Q
                    category_id = {$categoryId}
                    AND sort >= {$sort}
                ORDER BY sort DESC
                Q;
                CategoryProductSku::updateAllCounters(
                    ['sort' => 1],
                    $moveUpAllProductSkuWithHigherSortPosition
                );
                $categoryProductSkuEntity->sort = $sort;
                $this->save($categoryProductSkuEntity);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    /**
     * @param int $categoryId
     * @param int $productSkuId
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    public function createRelation(int $categoryId, int $productSkuId): void
    {
        $this->save(new CategoryProductSku([
            'category_id' => $categoryId,
            'product_sku_id' => $productSkuId,
            'sort' => $this->getMaxSort($categoryId) + 1,
        ]));
    }

    public function deleteRelation(int $categoryId, int $productSkuId): void
    {
        /** @var CategoryProductSku|null $categoryProductSkuEntity */
        $categoryProductSkuEntity = CategoryProductSku::find()
            ->where(['category_id' => $categoryId, 'product_sku_id' => $productSkuId])
            ->one();
        if (empty($categoryProductSkuEntity)) {
            throw new \Exception("Relation not exist for category id '{$categoryId}' and product sku id {$productSkuId}");
        }
        $this->delete($categoryProductSkuEntity);
        $moveDownAllProductSkuWithHigherSortPosition = <<<Q
                    category_id = {$categoryId}
                    AND sort > {$categoryProductSkuEntity->sort}
                ORDER BY sort ASC
                Q;
        CategoryProductSku::updateAllCounters(
            ['sort' => -1],
            $moveDownAllProductSkuWithHigherSortPosition
        );
    }
}