<?php
namespace DmitriiKoziuk\yii2Shop\services\supplier;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Shop\repositories\SupplierRepository;
use DmitriiKoziuk\yii2Shop\repositories\SupplierProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entities\SupplierProductSku;
use DmitriiKoziuk\yii2Shop\data\SupplierData;
use DmitriiKoziuk\yii2Shop\data\SupplierProductSkuData;
use DmitriiKoziuk\yii2Shop\forms\supplier\SupplierProductSkuCompositeUpdateForm;
use DmitriiKoziuk\yii2Shop\services\currency\CurrencyService;

final class SupplierService extends EntityActionService
{
    /**
     * @var SupplierRepository
     */
    private $_supplierRepository;

    /**
     * @var SupplierProductSkuRepository
     */
    private $_supplierProductSkuRepository;

    /**
     * @var CurrencyService
     */
    private $_currencyService;

    public function __construct(
        SupplierRepository $supplierRepository,
        SupplierProductSkuRepository $supplierProductSkuRepository,
        CurrencyService $currencyService,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_supplierRepository = $supplierRepository;
        $this->_supplierProductSkuRepository = $supplierProductSkuRepository;
        $this->_currencyService = $currencyService;
    }

    /**
     * @param int $productSkuID
     * @return SupplierData[]
     */
    public function getNonSelectedSuppliers(int $productSkuID): array
    {
        $suppliers = $this->_supplierRepository->getNonSelectedSuppliersForProductSku($productSkuID);
        $tmp = [];
        foreach ($suppliers as $supplier) {
            $tmp[] = new SupplierData($supplier);
        }
        return $tmp;
    }

    public function addProductSkuToSuppliers(array $suppliers, int $productSkuId): void
    {
        foreach ($suppliers as $supplierId => $value) {
            $this->addProductSku($supplierId, $productSkuId);
        }
    }

    public function addProductSku(int $supplierId, int $productSkuId): SupplierProductSku
    {
        $relation = new SupplierProductSku();
        $relation->supplier_id = $supplierId;
        $relation->product_sku_id = $productSkuId;
        $this->_supplierProductSkuRepository->save($relation);
        return $relation;
    }

    /**
     * @param int $productSkuId
     * @return SupplierProductSkuData[]
     */
    public function getAllProductSkuSuppliers(int $productSkuId): array
    {
        $productSkuSuppliers = [];
        $suppliers = $this->_supplierRepository->getProductSkuSuppliers($productSkuId);
        foreach ($suppliers as $supplier) {
            $supplierProductSku = $this->_supplierProductSkuRepository
                ->getProductSku($supplier->id, $productSkuId);
            $productSkuSuppliers[ $supplier->id ] = new SupplierProductSkuData(
                new SupplierData($supplier),
                $supplierProductSku
            );
        }
        return $productSkuSuppliers;
    }

    /**
     * @param array $productSkuIds
     * @return SupplierProductSkuData[][]
     */
    public function getProductSkusSuppliers(array $productSkuIds): array
    {
        $allCurrencies = $this->_currencyService->getAllCurrencies();
        $productSkuSuppliers = [];
        foreach ($productSkuIds as $productSkuId) {
            $suppliers = $this->_supplierRepository->getProductSkuSuppliers($productSkuId);
            foreach ($suppliers as $supplier) {
                $supplierProductSku = $this->_supplierProductSkuRepository
                    ->getProductSku($supplier->id, $productSkuId);
                $currentCurrency = $allCurrencies[ $supplierProductSku->currency_id ] ?? null;
                $productSkuSuppliers[ $productSkuId ][] = new SupplierProductSkuData(
                    new SupplierData($supplier),
                    $supplierProductSku,
                    $currentCurrency
                );
            }
        }
        return $productSkuSuppliers;
    }

    public function updateSupplierProductSkuData(
        SupplierProductSkuCompositeUpdateForm $compositeUpdateForm
    ): void {
        foreach ($compositeUpdateForm->getUpdateForms() as $form) {
            $supplierProductSkuRecord = $this->_supplierProductSkuRepository
                ->getProductSku($form->supplier_id, $form->product_sku_id);
            if (empty($supplierProductSkuRecord)) {
                throw new \Exception('Supplier product sku do not exist.');
            }
            $supplierProductSkuRecord->setAttributes($form->getUpdatedAttributes());
            $this->_supplierProductSkuRepository->save($supplierProductSkuRecord);
        }
    }
}