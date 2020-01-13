<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\services\product\ProductSkuSearchService;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;

class ProductViewWidget extends Widget
{
    public $categoryIDs = [];

    /**
     * @var ProductSkuSearchService
     */
    private $productSkuSearchService;

    public function __construct(
        ProductSkuSearchService $productSkuSearchService,
        $config = []
    ) {
        parent::__construct($config);
        $this->productSkuSearchService = $productSkuSearchService;
    }

    public function run()
    {
        $searchParams = new ProductSearchParams([
            'categoryIDs' => $this->categoryIDs,
        ]);
        $dataProvider = $this->productSkuSearchService->searchBy($searchParams, 20);
        $products = $this->productSkuModelsToData($dataProvider->getModels());
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