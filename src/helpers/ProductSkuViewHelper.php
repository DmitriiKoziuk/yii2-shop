<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\helpers;

class ProductSkuViewHelper
{
    private const CENT_IN_DOLLAR = 100;

    /**
     * @param int|null $price
     * @return string|null
     */
    public function priceFormat(int $price = null): ?string
    {
        if (empty($price)) {
            return null;
        }
        return number_format($price / self::CENT_IN_DOLLAR, 2, '.', '');
    }
}
