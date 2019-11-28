<?php

namespace DmitriiKoziuk\yii2Shop\widgets;

use Yii;
use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductData;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductSkuData;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\services\product\ProductSearchService;
use DmitriiKoziuk\yii2Shop\services\product\ProductSkuSearchService;

class ProductWidget extends Widget
{
    /**
     * @var ProductSearchParams
     */
    public $searchParams;

    /**
     * @var int
     */
    public $productPerPage = 2;

    /**
     * @var string
     */
    public $indexPageUrl;

    /**
     * @var EavAttributeEntity[]
     */
    public $filteredAttributes;

    /**
     * @var ProductData[]
     */
    private $_products = [];

    /**
     * @var \yii\data\Pagination
     */
    private $_pagination;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        parent::init();
        /** @var ProductSearchService $productSearchService */
        $productSearchService = Yii::$container->get(ProductSearchService::class);
        /** @var ProductSkuSearchService $productSkuSearchService */
        $productSkuSearchService = Yii::$container->get(ProductSkuSearchService::class);

        if (empty($this->filteredAttributes)) {
            $dataProvider = $productSearchService->searchBy(
                $this->searchParams,
                $this->productPerPage,
                $this->filteredAttributes
            );
            $this->_products = $this->_productModelsToData($dataProvider->getModels());
        } else {
            $dataProvider = $productSkuSearchService->searchBy(
                $this->searchParams,
                $this->productPerPage,
                $this->filteredAttributes
            );
            $this->_products = $this->productSkuModelsToData($dataProvider->getModels());
        }
        $this->_pagination = $dataProvider->getPagination();
    }

    public function run()
    {
        return $this->render('products', [
            'products' => $this->_products,
            'pagination' => $this->_pagination,
            'indexPageUrl' => $this->indexPageUrl,
        ]);
    }

    /**
     * @param Product[] $models
     * @return ProductData[]
     */
    private function _productModelsToData(array $models): array
    {
        $list = [];
        foreach ($models as $model) {
            $list[] = new ProductData($model);
        }
        return $list;
    }

    /**
     * @param Product[] $models
     * @return ProductData[]
     */
    private function productSkuModelsToData(array $models): array
    {
        $list = [];
        foreach ($models as $model) {
            $list[] = new ProductSkuData($model);
        }
        return $list;
    }
}
