<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\jobs\product;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;

class ProductAndSkuUpdateUrlWhenProductTypeUrlPrefixChangeJob extends BaseObject implements JobInterface
{
    /**
     * @var int
     */
    public $productTypeId;

    public function execute($queue)
    {
        /** @var ProductService $productService */
        $productService = Yii::$container->get(ProductService::class);
        $productQuery = Product::find()
            ->with(['skus', 'type'])
            ->where(['type_id' => $this->productTypeId]);
        /** @var Product[] $productEntities */
        foreach ($productQuery->batch(10) as $productEntities) {
            foreach ($productEntities as $product) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $productService->updateProductUrl($product);
                    foreach ($product->skus as $sku) {
                        $productService->updateProductSkuUrl($product, $sku);
                    }
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
    }
}
