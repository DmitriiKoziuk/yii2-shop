<?php
namespace DmitriiKoziuk\yii2Shop\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\data\product\ProductSkuSearchParams;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;

class UpdateProductSkuSellPriceJob extends BaseObject implements JobInterface
{
    /**
     * @var ProductSkuSearchParams
     */
    public $productSkuSearchParams;

    public function init()
    {
        if (empty($this->productSkuSearchParams)) {
            $this->productSkuSearchParams = new ProductSkuSearchParams();
        }
    }

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
        $query = $productSkuRepository->searchProductSku($this->productSkuSearchParams);
        /** @var ProductSku[] $productSkuRecordList */
        foreach ($query->batch(10) as $productSkuRecordList) {
            foreach ($productSkuRecordList as $productSkuRecord) {
                $productService->updateProductSkuSellPrice($productSkuRecord->id);
            }
        }
    }
}