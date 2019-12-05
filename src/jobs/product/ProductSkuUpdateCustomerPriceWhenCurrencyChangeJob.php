<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\jobs\product;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;
use DmitriiKoziuk\yii2Shop\repositories\CurrencyRepository;

class ProductSkuUpdateCustomerPriceWhenCurrencyChangeJob extends BaseObject implements JobInterface
{
    public $currencyId;

    public function execute($queue): void
    {
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = Yii::$container->get(CurrencyRepository::class);
        /** @var ProductService $productService */
        $productService = Yii::$container->get(ProductService::class);
        $currencyEntity = $currencyRepository->getCurrencyById($this->currencyId);
        if (empty($currencyEntity)) {
            throw new \Exception("Currency with id '{$this->currencyId}' not exist.");
        }
        $productSkuQuery = ProductSku::find()
            ->where(['currency_id' => $currencyEntity->id])
            ->andWhere(['is not', 'sell_price', null]);
        /** @var ProductSku[] $productSkuList */
        foreach ($productSkuQuery->batch(10) as $productSkuList) {
            foreach ($productSkuList as $productSku) {
                $productService->updateProductSkuCustomerPrice($productSku);
            }
        }
    }
}
