<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\SupplierProductSku;
use DmitriiKoziuk\yii2Shop\forms\supplier\SupplierProductSkuUpdateForm;

class SupplierProductSkuData
{
    /**
     * @var SupplierData
     */
    private $_supplierData;

    /**
     * @var SupplierProductSku
     */
    private $_supplierProductSku;

    /**
     * @var CurrencyData
     */
    private $_currencyData;

    public function __construct(
        SupplierData $supplierData,
        SupplierProductSku $supplierProductSku,
        CurrencyData $currencyData = null
    ) {
        $this->_supplierData = $supplierData;
        $this->_supplierProductSku = $supplierProductSku;
        $this->_currencyData = $currencyData;
    }

    public function getSuppliedData(): SupplierData
    {
        return $this->_supplierData;
    }

    public function getUniqueId(): string
    {
        return $this->_dataToString($this->_supplierProductSku->supplier_product_unique_id);
    }

    public function getQuantity(): string
    {
        return $this->_dataToString($this->_supplierProductSku->quantity);
    }

    public function getPurchasePrice(): string
    {
        return $this->_dataToString($this->_supplierProductSku->purchase_price);
    }

    public function getRecommendedSellPrice(): string
    {
        return $this->_dataToString($this->_supplierProductSku->recommended_sell_price);
    }

    public function getRecommendedProfit(): string
    {
        $r = '';
        if (
            ! empty($this->_supplierProductSku->purchase_price) &&
            ! empty($this->_supplierProductSku->recommended_sell_price)
        ) {
            $r = strval($this->_supplierProductSku->recommended_sell_price - $this->_supplierProductSku->purchase_price);
        }
        return $r;
    }

    public function getActualProfit(float $productSkuSellPrice): string
    {
        if (! empty($this->_supplierProductSku->purchase_price) && ! empty($productSkuSellPrice)) {
            return floatval($productSkuSellPrice - $this->_supplierProductSku->purchase_price);
        } else {
            return '';
        }
    }

    public function getCurrencyName(): string
    {
        if (empty($this->_currencyData)) {
            return '';
        } else {
            return $this->_currencyData->getName();
        }
    }

    public function getCurrencyId(): int
    {
        return $this->_supplierProductSku->currency_id;
    }

    public function getUpdateForm()
    {
        $form = new SupplierProductSkuUpdateForm();
        $form->setAttributes($this->_supplierProductSku->getAttributes());
        return $form;
    }

    private function _dataToString($data): string
    {
        return $data == null ? 'not set' : (string) $data;
    }
}