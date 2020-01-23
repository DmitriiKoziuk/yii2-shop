<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;

class ProductViewWidget extends Widget
{
    public $categoryIDs = [];

    /**
     * @var ProductSkuRepository
     */
    private $productSkuRepository;

    public function __construct(
        ProductSkuRepository $productSkuRepository,
        $config = []
    ) {
        parent::__construct($config);
        $this->productSkuRepository = $productSkuRepository;
    }

    public function run()
    {
        $searchParams = new ProductSearchParams([
            'categoryIDs' => $this->categoryIDs,
            'limit' => 20,
        ]);
        $searchResponse = $this->productSkuRepository->search($searchParams);
        $products = $this->productSkuModelsToData($searchResponse->getItems());
        return $this->render('product-view', [
            'products' => $products,
        ]);
    }

    /**
     * @param Product[] $models
     * @return ProductSkuView[]
     */
    private function productSkuModelsToData(array $models): array
    {
        $list = [];
        foreach ($models as $model) {
            $list[] = new ProductSkuView($model);
        }
        return $list;
    }
}