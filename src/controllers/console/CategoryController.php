<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\console;

use Yii;
use yii\console\Controller;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\repositories\CategoryProductSkuRepository;

class CategoryController extends Controller
{
    private $categoryProductSkuRepository;

    public function __construct(
        $id,
        $module,
        CategoryProductSkuRepository $categoryProductSkuRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->categoryProductSkuRepository = $categoryProductSkuRepository;
    }

    public function actionIndex()
    {
        $this->stdout("Hello\n");
        $this->stdout("---commands---\n");
        $this->stdout("sort-out-of-stock-products-to-end\n");
        return 0;
    }

    public function actionSortOutOfStockProductsToEnd()
    {
        $productSkuQuery = ProductSku::find()
            ->with(['product.category.parentList'])
            ->where([
                'stock_status' => ProductSku::STOCK_OUT,
            ])
            ->orderBy(['id' => SORT_ASC]);

        /** @var ProductSku[] $productSkuList */
        foreach ($productSkuQuery->batch(10) as $productSkuList) {
            foreach ($productSkuList as $productSku) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $category = $productSku->product->category;
                    if (! empty($category)) {
                        $this->categoryProductSkuRepository->updateSort(
                            $category->id,
                            $productSku->id,
                            $this->categoryProductSkuRepository->getMaxSort($category->id)
                        );

                        if (! empty($category->parentList)) {
                            foreach ($category->parentList as $parentCategory) {
                                $this->categoryProductSkuRepository->updateSort(
                                    $parentCategory->id,
                                    $productSku->id,
                                    $this->categoryProductSkuRepository->getMaxSort($parentCategory->id)
                                );
                            }
                        }
                    }

                    $transaction->commit();
                    $this->stdout("Product sku with id '{$productSku->id}' move to end.\n");
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    break;
                }
            }
        }


        $this->stdout("Done.\n");
        return 0;
    }
}