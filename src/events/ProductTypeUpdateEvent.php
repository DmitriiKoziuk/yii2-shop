<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\events;

use yii\base\Event;

class ProductTypeUpdateEvent extends Event
{
    const EVENT_PRODUCT_TYPE_UPDATE = 'product-type-update';

    public $changedAttributes;

    public $productTypeAttributes;
}
