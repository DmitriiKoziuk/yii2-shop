<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2Shop\entities\Currency;

final class CurrencyRepository extends EntityRepository
{
    public function getCurrencyById($id): ?Currency
    {
        /** @var Currency $currency */
        $currency = Currency::find()->where(['id' => $id])->one();
        return $currency;
    }
}