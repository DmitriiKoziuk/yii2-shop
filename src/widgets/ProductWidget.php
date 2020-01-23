<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use yii\data\Pagination;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entityViews\ProductEntityView;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;

class ProductWidget extends Widget
{
    /**
     * @var Product[]|ProductSku[]
     */
    public $products;

    /**
     * @var Pagination
     */
    public $pagination;

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

    public function run()
    {
        if (! empty($this->products)) {
            if (empty($this->filterParams)) {
                $this->_products = $this->productModelsToData($this->products);
            } else {
                $this->_products = $this->productSkuModelsToData($this->products);
            }
            return $this->render('product', [
                'products' => $this->_products,
                'pagination' => $this->pagination,
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
