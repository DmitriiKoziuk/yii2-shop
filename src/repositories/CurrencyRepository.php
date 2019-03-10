<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Currency;

class CurrencyRepository extends AbstractActiveRecordRepository
{
    /**
     * @return Currency[]
     */
    public function getAllCurrencies(): array
    {
        $currencies = Currency::find()->all();
        return $currencies;
    }

    public function getCurrencyById($id): ?Currency
    {
        /** @var Currency $currency */
        $currency = Currency::find()->where(['id' => $id])->one();
        return $currency;
    }
}