<?php

namespace DmitriiKoziuk\yii2Shop\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2FileManager\entities\File;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2FileManager\helpers\FileHelper;
use DmitriiKoziuk\yii2Shop\entities\SupplierPrice;
use DmitriiKoziuk\yii2Shop\data\SupplierData;
use DmitriiKoziuk\yii2Shop\data\SupplierPriceData;
use DmitriiKoziuk\yii2Shop\data\ProductSkuData;
use DmitriiKoziuk\yii2Shop\forms\product\ProductInputForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductSkuCreateForm;
use DmitriiKoziuk\yii2Shop\forms\supplier\SupplierProductSkuUpdateForm;
use DmitriiKoziuk\yii2Shop\services\currency\CurrencyService;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierService;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierPriceService;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;
use DmitriiKoziuk\yii2Shop\repositories\SupplierProductSkuRepository;

class ProcessSupplierPriceJob extends BaseObject implements JobInterface
{
    /**
     * @var int
     */
    public $supplierPriceId;

    /**
     * @var SupplierPriceData
     */
    private $_supplierPriceData;

    /**
     * @var array
     */
    private $_currencies;

    /**
     * @var SupplierService
     */
    private $_supplierService;

    /**
     * @var SupplierProductSkuRepository
     */
    private $_supplierProductSkuRepository;

    /**
     * @var SupplierData
     */
    private $_supplierData;

    /**
     * @var File
     */
    private $_pricePath;

    /**
     * @var ProductService
     */
    private $_productService;

    /**
     * @var array
     */
    private $_baseRequiredColumns = [
        '{productUniqueId}',
    ];

    private $_createProductSkuRequiredColumns = [
        '{productUniqueId}',
        '{productName}',
    ];

    private $_updateSupplierPurchasePriceRequiredColumns = [
        '{productUniqueId}',
        '{purchasePrice}',
        '{currencyCode}',
    ];

    /**
     * @throws \Exception
     */
    private function _init()
    {
        try {
            if (empty($this->supplierPriceId)) {
                throw new \BadMethodCallException("Property 'supplierPriceId' not set.");
            }

            /** @var SupplierPriceService $supplierPriceService */
            $supplierPriceService = Yii::$container->get(SupplierPriceService::class);
            $this->_supplierPriceData = $supplierPriceService->getSupplierPriceById($this->supplierPriceId);
            if (empty($this->_supplierPriceData)) {
                throw new \BadMethodCallException("Supplier price with id '{$this->supplierPriceId}' not exist.");
            }

            /** @var CurrencyService $currencyService */
            $currencyService = Yii::$container->get(CurrencyService::class);
            $this->_currencies = ArrayHelper::map(
                $currencyService->getAllCurrencies(),
                'code',
                'id'
            );

            /** @var SupplierService _supplierService */
            $this->_supplierService = Yii::$container->get(SupplierService::class);
            $this->_supplierData = $this->_supplierService->getSupplierById($this->_supplierPriceData->getSupplierId());
            if (empty($this->_supplierData)) {
                throw new \BadMethodCallException("Supplier with id '{$this->_supplierPriceData->getSupplierId()}' not exist.");
            }

            $this->_supplierProductSkuRepository = Yii::$container->get(SupplierProductSkuRepository::class);

            /** @var FileService $fileService */
            $fileService = Yii::$container->get(FileService::class);
            /** @var File[] $files */
            $files = $fileService->getAllFiles(
                SupplierPrice::FILE_ENTITY_NAME,
                $this->_supplierPriceData->getId()
            );
            if (empty($files)) {
                throw new \BadMethodCallException("Do not upload price for '{$this->supplierPriceId}'.");
            }
            /** @var File $file */
            $file = array_shift($files);
            /** @var FileHelper $fileHelper */
            $fileHelper = Yii::$container->get(FileHelper::class);
            $this->_pricePath = $fileHelper->getFileRecordFullPath($file);
            $this->_productService = Yii::$container->get(ProductService::class);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'dk-shop-job');
            throw $e;
        }
    }

    /**
     * @param \yii\queue\Queue $queue
     * @throws \Exception
     */
    public function execute($queue)
    {
        $this->_init();
        try {
            $columnTemplates = null;
            $rowNumber = 1;
            if (false !== ($fp = fopen($this->_pricePath, 'r'))) {
                while (false !== ($rowData = fgetcsv($fp))) {
                    if (1 == $rowNumber) {
                        $this->_isBaseRequiredColumnsSet($rowData);
                        $columnTemplates = $rowData;
                    } else {
                        $productSkuData = $this->_findProductSku($columnTemplates, $rowData);
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if (
                                empty($productSkuData) &&
                                $this->_isCreateProductSkuRequiredColumnsSet($columnTemplates)
                            ) {
                                $productSkuData = $this->_createProduct($columnTemplates, $rowData);
                            }
                            if (
                                ! empty($productSkuData) &&
                                $this->_isUpdatePurchasePriceRequiredColumnsSet($columnTemplates)
                            ) {
                                $this->_updatePurchasePrice($productSkuData, $columnTemplates, $rowData);
                            }
                            $transaction->commit();
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                    $rowNumber++;
                }
            } else {
                throw new \BadMethodCallException("Can't open file '{$this->_pricePath}'.");
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'dk-shop-job');
            throw $e;
        } finally {
            if (isset($fp) && is_resource($fp)) {
                fclose($fp);
            }
        }
    }

    private function _findProductSku(array $columnTemplates, array $rowData): ?ProductSkuData
    {
        // try to find product sku by supplier product id
        $productSkuData = $this->_getProductSkuBySupplierUniqueId(
            $this->_getSupplierProductUniqueId($columnTemplates, $rowData)
        );
        // try to find product by name and then take main product sku
        if (
            empty($productSkuData) &&
            false !== ($key = array_search('{productName}', $columnTemplates))
        ) {
            $productData = $this->_productService->getProductByName($key);
            if (! empty($productData)) {
                $productSkuData = $this->_productService
                    ->getProductSkuById($productData->getMainSkuId());
                if (! empty($productSkuData)) {
                    $this->_addProductToSupplier($productSkuData, $columnTemplates, $rowData);
                }
            }
        }
        return $productSkuData;
    }

    private function _isCreateProductSkuRequiredColumnsSet(array $columns): bool
    {
        foreach ($this->_createProductSkuRequiredColumns as $requiredColumn) {
            if (false === array_search($requiredColumn, $columns)) {
                return false;
            }
        }
        return true;
    }

    private function _isUpdatePurchasePriceRequiredColumnsSet(array $columns): bool
    {
        foreach ($this->_updateSupplierPurchasePriceRequiredColumns as $requiredColumn) {
            if (false === array_search($requiredColumn, $columns)) {
                return false;
            }
        }
        return true;
    }

    private function _isBaseRequiredColumnsSet(array $columns): bool
    {
        foreach ($this->_baseRequiredColumns as $requiredColumn) {
            if (false === array_search($requiredColumn, $columns)) {
                throw new \BadMethodCallException("Do not set column '{$requiredColumn}'");
            }
        }
        return true;
    }

    private function _getProductSkuBySupplierUniqueId(string $supplierProductUniqueId): ?ProductSkuData
    {
        $productSkuId = $this->_supplierService
            ->getProductSkuBySupplierUniqueProductId($supplierProductUniqueId);
        if (empty($productSkuId)) {
            return null;
        }
        return $this->_productService->getProductSkuById($productSkuId);
    }

    private function _getSupplierProductUniqueId(array $columnTemplates, array $rowData): string
    {
        $uniqueIdKey = array_search('{productUniqueId}', $columnTemplates);
        $id = trim($rowData[ $uniqueIdKey ]);
        return $id;
    }

    private function _createProduct(array $columnTemplates, array $rowData): ProductSkuData
    {
        $productInputForm = new ProductInputForm(['scenario' => ProductInputForm::SCENARIO_CREATE]);
        $productSkuInputForm = new ProductSkuCreateForm();
        foreach ($rowData as $key => $data) {
            switch ($columnTemplates[ $key ]) {
                case '{productName}':
                    $productInputForm->name = $data;
                    break;
                default :
                    break;
            }
        }
        $productEntity = $this->_productService->create($productInputForm, $productSkuInputForm);
        $productSkuData = $this->_productService
            ->getProductSkuById($productEntity->main_sku_id);
        $this->_addProductToSupplier($productSkuData, $columnTemplates, $rowData);
        return $productSkuData;
    }

    private function _addProductToSupplier(
        ProductSkuData $productSkuData,
        array $columnTemplates,
        array $rowData
    ): void {
        $supplierProductUniqueId = null;
        if (false !== ($key = array_search('{productUniqueId}', $columnTemplates))) {
            $supplierProductUniqueId = $rowData[ $key ];
        }
        $this->_supplierService->addProductSku(
            $this->_supplierData->getId(),
            $productSkuData->getId(),
            $supplierProductUniqueId
        );
    }

    private function _updatePurchasePrice(
        ProductSkuData $productSkuData,
        array $columnTemplates,
        array $rowData
    ) {
        $supplierProductSkuEntity = $this->_supplierProductSkuRepository
            ->getProductSku($this->_supplierData->getId(), $productSkuData->getId());
        $form = new SupplierProductSkuUpdateForm();
        $form->setAttributes($supplierProductSkuEntity->getAttributes());
        foreach ($rowData as $key => $value) {
            switch ($columnTemplates[ $key ]) {
                case '{purchasePrice}':
                    $form->purchase_price = str_replace(',', '.', trim($value));
                    break;
                case '{currencyCode}':
                    $form->currency_id = $this->_currencies[ $value ];
                    break;
                default :
                    break;
            }
        }
        $this->_supplierService->updateSupplierProductSkuData($form, false);
    }
}
