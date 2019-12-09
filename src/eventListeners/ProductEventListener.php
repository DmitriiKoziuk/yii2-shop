<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\eventListeners;

use yii\base\Event;
use yii\queue\cli\Queue;
use DmitriiKoziuk\yii2Shop\events\CurrencyUpdateEvent;
use DmitriiKoziuk\yii2Shop\events\ProductTypeUpdateEvent;
use DmitriiKoziuk\yii2Shop\jobs\product\ProductSkuUpdateCustomerPriceWhenCurrencyChangeJob;
use DmitriiKoziuk\yii2Shop\jobs\product\ProductAndSkuUpdateUrlWhenProductTypeUrlPrefixChangeJob;

class ProductEventListener
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
        $this->updateProductSkuCurrencyRate();
        $this->updateUrl();
    }

    private function updateProductSkuCurrencyRate(): void
    {
        Event::on(
            CurrencyUpdateEvent::class,
            CurrencyUpdateEvent::EVENT_CURRENCY_UPDATE,
            function (CurrencyUpdateEvent $event) {
                if (array_key_exists('rate', $event->changedAttributes)) {
                    $this->queue->push(new ProductSkuUpdateCustomerPriceWhenCurrencyChangeJob([
                        'currencyId' => $event->currencyAttributes['id'],
                    ]));
                }
            }
        );
    }

    private function updateUrl(): void
    {
        Event::on(
            ProductTypeUpdateEvent::class,
            ProductTypeUpdateEvent::EVENT_PRODUCT_TYPE_UPDATE,
            function (ProductTypeUpdateEvent $event) {
                if (array_key_exists('product_url_prefix', $event->changedAttributes)) {
                    $this->queue->push(new ProductAndSkuUpdateUrlWhenProductTypeUrlPrefixChangeJob([
                        'productTypeId' => $event->productTypeAttributes['id'],
                    ]));
                }
            }
        );
    }
}
