<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\events;

use yii\base\Event;

class ProductTypeBeforeDeleteEvent extends Event
{
    const EVENT_BEFORE_DELETE = 'before-delete';

    public $productTypeId;
}
