<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\repositories;

use yii\db\Expression;
use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\interfaces\RepositorySearchMethodResponseInterface;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\data\RepositorySearchMethodResponse;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\CategoryProductSku;

class ProductRepository extends AbstractActiveRecordRepository
{
    public function getById(int $id): ?Product
    {
        /** @var Product|null $product */
        $product = Product::find()->where(['id' => $id])->one();
        return $product;
    }

    public function getByName(string $name): ?Product
    {
        /** @var Product|null $productEntity */
        $productEntity = Product::find()->where(['name' => $name])->one();
        return $productEntity;
    }

    /**
     * @param ProductSearchParams $params
     * @param int $limit
     * @param null|int $offset
     * @return RepositorySearchMethodResponseInterface
     */
    public function searchByName(
        ProductSearchParams $params,
        int $limit = 10,
        int $offset = null
    ): RepositorySearchMethodResponseInterface {
        $query = Product::find()
            ->where(['like', 'name', $params->getName()])
            ->limit($limit);
        if (! empty($offset)) {
            $query->offset($offset);
        }
        return new RepositorySearchMethodResponse((int) $query->count(), $query->all());
    }

    public function search(
        ProductSearchParams $params,
        array $with = [
            'mainSkuEntity.urlEntity',
            'mainSkuEntity.product',
            'mainSkuEntity.product.type.productPreviewEavAttributes',
            'mainSkuEntity.currency',
            'mainSkuEntity.eavVarcharValues.eavAttribute',
            'mainSkuEntity.eavTextValues.eavAttribute',
            'mainSkuEntity.eavDoubleValues.eavAttribute',
            'mainSkuEntity.mainImageEntity',
        ]
    ): RepositorySearchMethodResponseInterface {
        if (! $params->validate()) {
            throw new \BadMethodCallException('Search params not valid.');
        }

        $query = Product::find();
        if (! empty($with)) {
            $query->with($with);
        }
        if (! empty($params->getCategoryIDs())) {
            $query->addSelect([
                Product::tableName() . '.*',
                CategoryProductSku::tableName() . '.sort',
            ]);
            $query->innerJoin(
                CategoryProductSku::tableName(),
                [
                    CategoryProductSku::tableName() . '.product_sku_id'  => new Expression(
                        Product::tableName() . '.main_sku_id'
                    ),
                ]
            );
            $query->andWhere([
                CategoryProductSku::tableName() . '.category_id' => $params->getCategoryIDs(),
            ]);
            $query->orderBy([
                CategoryProductSku::tableName() . '.sort' => SORT_ASC,
            ]);
        }
        $productCount = $query->count();
        $query->limit = $params->getLimit();
        if ($params->isOffsetSet()) {
            $query->offset = $params->getOffset();
        }
        return new RepositorySearchMethodResponse((int) $productCount, $query->all());
    }
}