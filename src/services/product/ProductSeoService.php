<?php
namespace DmitriiKoziuk\yii2Shop\services\product;

use DmitriiKoziuk\yii2Shop\data\ProductData;
use DmitriiKoziuk\yii2Shop\data\ProductSkuData;
use DmitriiKoziuk\yii2Shop\data\ProductTypeData;
use DmitriiKoziuk\yii2Shop\helpers\StringHelper;

class ProductSeoService
{
    private $_stringHelper;

    public function __construct(StringHelper $stringHelper)
    {
        $this->_stringHelper = $stringHelper;
    }

    public function getProductSkuMetaTitle(
        ProductData $productData,
        ProductSkuData $productSkuData,
        ProductTypeData $productTypeData = null
    ): string {
        if (empty($productTypeData)) {
            return $productSkuData->getMetaTitle() ?? '';
        } else {
            $string = $this->_stringHelper
                ->getStringFromTemplate(
                    $productTypeData->getProductSkuMetaTitleTemplate(),
                    [
                        'productName' => $productData->getName(),
                        'productSkuName' => $productSkuData->getName(),
                        'productSkuSitePrice' => $productSkuData->getPriceOnSite(),
                    ]
                );
            return $string ?? '';
        }
    }

    public function getProductSkuMetaDescription(
        ProductData $productData,
        ProductSkuData $productSkuData,
        ProductTypeData $productTypeData = null
    ): string {
        if (empty($productTypeData)) {
            return $productSkuData->getMetaTitle() ?? '';
        } else {
            $string = $this->_stringHelper
                ->getStringFromTemplate(
                    $productTypeData->getProductSkuMetaDescriptionTemplate(),
                    [
                        'productName' => $productData->getName(),
                        'productSkuName' => $productSkuData->getName(),
                        'productSkuSitePrice' => $productSkuData->getPriceOnSite(),
                    ]
                );
            return $string ?? '';
        }
    }
}