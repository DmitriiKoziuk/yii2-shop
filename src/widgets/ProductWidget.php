<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entityViews\ProductEntityView;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;

class ProductWidget extends Widget
{
    /**
     * @var ActiveDataProvider|null
     */
    public $productDataProvider;

    /**
     * @var string
     */
    public $indexPageUrl;

    /**
     * @var array
     */
    public $filterParams;

    /**
     * @var ProductEntityView[]|ProductSkuView[]
     */
    private $_products = [];

    /**
     * @var Pagination
     */
    private $_pagination;

    public function run()
    {
        if (! empty($this->productDataProvider)) {
            if (empty($this->filterParams)) {
                $this->_products = $this->productModelsToData($this->productDataProvider->getModels());
            } else {
                $this->_products = $this->productSkuModelsToData($this->productDataProvider->getModels());
            }
            $this->_pagination = $this->productDataProvider->getPagination();
            return $this->render('product', [
                'products' => $this->_products,
                'pagination' => $this->_pagination,
                'indexPageUrl' => $this->indexPageUrl,
                'filterParams' => $this->filterParams,
            ]);
        }
        return '';
    }

    /**
     * @param Product[] $models
     * @return ProductEntityView[]
     */
    private function productModelsToData(array $models): array
    {
        $list = [];
        foreach ($models as $model) {
            $list[] = new ProductEntityView($model);
        }
        return $list;
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
