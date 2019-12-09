<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\product;

use DmitriiKoziuk\yii2Shop\helpers\StringHelper;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;

class ProductSeoService
{
    private $_stringHelper;

    public function __construct(StringHelper $stringHelper)
    {
        $this->_stringHelper = $stringHelper;
    }

    public function getProductSkuMetaTitle(ProductSkuView $productSkuView): string
    {
        if (! $productSkuView->isTypeSet()) {
            return $productSkuView->getMetaTitle() ?? '';
        } else {
            $string = $this->_stringHelper
                ->getStringFromTemplate(
                    $productSkuView->getType()->getProductSkuTitleTemplate(),
                    [
                        'productName' => $productSkuView->getProductName(),
                        'productSkuName' => $productSkuView->getName(),
                        'customerPrice' => $productSkuView->getCustomerPrice(),
                    ]
                );
            return $string ?? '';
        }
    }

    public function getProductSkuMetaDescription(ProductSkuView $productSkuView): string {
        if (! $productSkuView->isTypeSet()) {
            return $productSkuView->getMetaTitle() ?? '';
        } else {
            $string = $this->_stringHelper
                ->getStringFromTemplate(
                    $productSkuView->getType()->getProductSkuDescriptionTemplate(),
                    [
                        'productName' => $productSkuView->getProductName(),
                        'productSkuName' => $productSkuView->getName(),
                        'customerPrice' => $productSkuView->getCustomerPrice(),
                    ]
                );
            return $string ?? '';
        }
    }
}
