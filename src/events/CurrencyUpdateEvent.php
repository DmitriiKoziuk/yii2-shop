<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\events;

use yii\base\Event;

class CurrencyUpdateEvent extends Event
{
    const EVENT_CURRENCY_UPDATE = 'currency-update';

    public $changedAttributes;

    public $currencyAttributes;
}
