<?php
namespace DmitriiKoziuk\yii2Shop\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\data\product\ProductSkuSearchParams;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;

class UpdateProductSellPriceJob extends BaseObject implements JobInterface
{
    /**
     * @var int
     */
    public $productTypeId;

    /**
     * @var int
     */
    public $currencyId = null;

    /**
     * @param \yii\queue\Queue $queue
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function execute($queue)
    {
        /** @var ProductSkuRepository $productSkuRepository */
        $productSkuRepository = Yii::$container->get(ProductSkuRepository::class);
        /** @var ProductService $productService */
        $productService = Yii::$container->get(ProductService::class);
        $searchParams = $this->_prepareSearchParams();
        $query = $productSkuRepository->searchProductSku($searchParams);
        /** @var ProductSku[] $productSkuRecordList */
        foreach ($query->batch(10) as $productSkuRecordList) {
            foreach ($productSkuRecordList as $productSkuRecord) {
                $productService->updateProductSkuSellPrice($productSkuRecord->id);
            }
        }
    }

    private function _prepareSearchParams(): ProductSkuSearchParams
    {
        $searchParams = new ProductSkuSearchParams();
        $searchParams->type_id = $this->productTypeId;
        if (! empty($this->currencyId)) {
            $searchParams->currency_id = $this->currencyId;
        }
        $searchParams->sell_price_strategy = ProductSku::SELL_PRICE_STRATEGY_MARGIN;
        return $searchParams;
    }
}