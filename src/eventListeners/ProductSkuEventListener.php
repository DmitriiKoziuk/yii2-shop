<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\eventListeners;

use yii\base\Event;
use yii\queue\cli\Queue;
use DmitriiKoziuk\yii2Shop\events\CurrencyUpdateEvent;
use DmitriiKoziuk\yii2Shop\jobs\product\ProductSkuUpdateCustomerPriceWhenCurrencyChange;

class ProductSkuEventListener
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
        $this->updateProductSkuCurrencyRate();
    }

    private function updateProductSkuCurrencyRate()
    {
        Event::on(
            CurrencyUpdateEvent::class,
            CurrencyUpdateEvent::EVENT_CURRENCY_UPDATE,
            function (CurrencyUpdateEvent $event) {
                if (array_key_exists('rate', $event->changedAttributes)) {
                    $this->queue->push(new ProductSkuUpdateCustomerPriceWhenCurrencyChange([
                        'currencyId' => $event->currencyAttributes['id'],
                    ]));
                }
            }
        );
    }
}
