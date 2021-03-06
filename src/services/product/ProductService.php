<?php

namespace DmitriiKoziuk\yii2Shop\services\product;

use Exception;
use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UpdateEntityUrlForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\helpers\UrlHelper;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeMargin;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\data\ProductData;
use DmitriiKoziuk\yii2Shop\data\ProductSkuData;
use DmitriiKoziuk\yii2Shop\data\ProductTypeMarginData;
use DmitriiKoziuk\yii2Shop\data\ProductTypeData;
use DmitriiKoziuk\yii2Shop\data\SupplierProductSkuData;
use DmitriiKoziuk\yii2Shop\forms\product\ProductUpdateForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductCreateForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductSkuUpdateForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductSkuCreateForm;
use DmitriiKoziuk\yii2Shop\repositories\ProductRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryProductSkuRepository;
use DmitriiKoziuk\yii2Shop\services\eav\EavService;
use DmitriiKoziuk\yii2Shop\services\eav\ProductSkuEavAttributesService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryProductService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryProductSkuService;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierService;
use DmitriiKoziuk\yii2Shop\services\currency\CurrencyService;

class ProductService extends DBActionService
{
    /**
     * @var ProductRepository
     */
    private $_productRepository;

    /**
     * @var ProductSkuRepository
     */
    private $_productSkuRepository;

    /**
     * @var ProductTypeService
     */
    private $_productTypeService;

    /**
     * @var ProductMarginService
     */
    private $_productTypeMarginService;

    /**
     * @var ProductSkuEavAttributesService
     */
    private $productSkuEavAttributesService;

    /**
     * @var SupplierService
     */
    private $_supplierService;

    /**
     * @var UrlIndexService
     */
    private $_urlIndexService;

    /**
     * @var CategoryProductService
     */
    private $_categoryProductService;

    /**
     * @var CategoryProductSkuService
     */
    private $_categoryProductSkuService;

    /**
     * @var CategoryProductSkuRepository
     */
    private $categoryProductSkuRepository;

    /**
     * @var CurrencyService
     */
    private $_currencyService;

    /**
     * @var EavService
     */
    private $eavService;

    public function __construct(
        ProductRepository $productRepository,
        ProductSkuRepository $_productSkuRepository,
        ProductTypeService $productTypeService,
        ProductMarginService $productTypeMarginService,
        ProductSkuEavAttributesService $productSkuEavAttributesService,
        SupplierService $supplierService,
        UrlIndexService $urlIndexService,
        CategoryProductService $categoryProductService,
        CategoryProductSkuService $categoryProductSkuService,
        CategoryProductSkuRepository $categoryProductSkuRepository,
        CurrencyService $currencyService,
        EavService $eavService,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_productRepository = $productRepository;
        $this->_productSkuRepository = $_productSkuRepository;
        $this->_productTypeService = $productTypeService;
        $this->_productTypeMarginService = $productTypeMarginService;
        $this->productSkuEavAttributesService = $productSkuEavAttributesService;
        $this->_supplierService = $supplierService;
        $this->_urlIndexService = $urlIndexService;
        $this->_categoryProductService = $categoryProductService;
        $this->_categoryProductSkuService = $categoryProductSkuService;
        $this->categoryProductSkuRepository = $categoryProductSkuRepository;
        $this->_currencyService = $currencyService;
        $this->eavService = $eavService;
    }

    /**
     * @param ProductCreateForm $productCreateForm
     * @param ProductSkuCreateForm $productSkuCreateForm
     * @return Product
     * @throws \Throwable
     */
    public function create(
        ProductCreateForm $productCreateForm,
        ProductSkuCreateForm $productSkuCreateForm
    ): Product {
        $this->beginTransaction();
        try {
            $product = $this->_createProduct($productCreateForm);
            $productSku = $this->_createProductSku($product, $productSkuCreateForm);
            $this->_setMainSkuId($product, $productSku);
            $this->commitTransaction();
            return $product; //TODO return ProductInputForm and ProductSkuInputForm[].
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param int $productId
     * @param ProductUpdateForm $productInputForm
     * @param ProductSkuUpdateForm[] $productSkuInputForms
     * @return Product
     * @throws \Throwable
     */
    public function update(
        int $productId,
        ProductUpdateForm $productInputForm,
        array $productSkuInputForms
    ): Product {
        try {
            $this->beginTransaction();
            $product = $this->_productRepository->getById($productId);
            $productChangedAttributes = $this->_updateProduct($product, $productInputForm);
            $this->_updateProductCategoryRelation($product, $productChangedAttributes);
            foreach ($productSkuInputForms as $productSkuInputForm) {
                $productSku = $this->_productSkuRepository->getById($productSkuInputForm->id);
                $this->_updateProductSku(
                    $product,
                    $productSku,
                    $productSkuInputForm,
                    $productChangedAttributes
                );
                $this->_updateProductSkuCategoryRelation(
                    $product,
                    $productSku,
                    $productChangedAttributes
                );
            }
            $this->commitTransaction();
            return $product; //TODO return ProductInputForm and ProductSkuInputForm[].
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param Product $product
     * @param ProductSkuCreateForm $productSkuCreateForm
     * @return ProductSku
     * @throws \Throwable
     */
    public function addNewSkuToProduct(
        Product $product,
        ProductSkuCreateForm $productSkuCreateForm
    ): ProductSku { //TODO change Product $product to ProductInputForm $productInputForm.
        try {
            $this->beginTransaction();
            $productSku = $this->_createProductSku($product, $productSkuCreateForm);
            $this->commitTransaction();
            return $productSku; //TODO return ProductSkuInputForm with new product sku date.
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param int $productId
     * @return ProductData
     * @throws EntityNotFoundException
     */
    public function getProductById(int $productId): ProductData
    {
        $productRecord = $this->_productRepository->getById($productId);
        if (empty($productRecord)) {
            throw new EntityNotFoundException("Product sku with id '{$productId}' not found.");
        }
        return new ProductData($productRecord);
    }

    public function getProductByName(string $name): ?ProductData
    {
        $productEntity = $this->_productRepository->getByName($name);
        if (empty($productEntity)) {
            return null;
        }
        return new ProductData($productEntity);
    }

    /**
     * @param int $productSkuId
     * @return ProductSkuData
     * @throws EntityNotFoundException
     */
    public function getProductSkuById(int $productSkuId): ProductSkuData
    {
        $productSkuRecord = $this->_productSkuRepository->getById($productSkuId);
        if (empty($productSkuRecord)) {
            throw new EntityNotFoundException("Product sku with id '{$productSkuId}' not found.");
        }
        return new ProductSkuData($productSkuRecord);
    }

    public function updateProductSkuSellPrice(int $productSkuId): void
    {
        $productSkuRecord = $this->_productSkuRepository->getById($productSkuId);
        if (! empty($productSkuRecord)) {
            $this->_defineProductSkuSellPrice($productSkuRecord);
            $this->_defineProductSkuCustomerPrice($productSkuRecord);
            $this->_productSkuRepository->save($productSkuRecord);
        }
    }

    public function updateProductSkuCustomerPrice(ProductSku $productSku): void
    {
        $this->_defineProductSkuCustomerPrice($productSku);
        $this->_productSkuRepository->save($productSku);
    }

    /**
     * @param Product $product
     * @param ProductSku $productSku
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    private function _setMainSkuId(Product $product, ProductSku $productSku)
    {
        $product->main_sku_id = $productSku->id;
        $this->_productRepository->save($product);
    }

    /**
     * @param ProductCreateForm $productCreateForm
     * @return Product
     * @throws EntityNotFoundException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    private function _createProduct(ProductCreateForm $productCreateForm): Product
    {
        $product = new Product();
        $product->setAttributes($productCreateForm->getAttributes());
        $product->slug = $this->defineProductSlug($product->name);
        $this->_productRepository->save($product);
        $url = $this->_defineProductUrl($product);
        $this->_addProductUrlToIndex($product, $url);
        return $product;
    }

    /**
     * @param Product $product
     * @param ProductUpdateForm $productInputForm
     * @return array product changed attributes
     * @throws \Throwable
     */
    private function _updateProduct(
        Product $product,
        ProductUpdateForm $productInputForm
    ): array {
        $changedAttributes = [];
        $product->setAttributes($productInputForm->getAttributes(null, ['url']));
        if ($product->isAttributeChanged('name')) {
            if (! $product->isAttributeChanged('slug')) {
                $product->slug = $this->defineProductSlug($product->name);
            } else {
                $product->slug = $this->defineProductSlug($product->slug);
            }
        }
        if (
            $product->isAttributeChanged('type_id') &&
            ! empty($product->getOldAttribute('type_id'))
        ) {
            $this->eavService->removeAttributesFromProduct($product);
        }
        if (
            $product->isAttributeChanged('slug') ||
            $product->isAttributeChanged('type_id')
        ) {
            $changedAttributes['url'] = $this->updateProductUrl($product);
        }
        $changedAttributes = array_merge($changedAttributes, $product->getDirtyAttributes());
        $this->_productRepository->save($product);
        return $changedAttributes;
    }

    public function updateProductUrl(Product $productEntity): string
    {
        $url = $this->_defineProductUrl($productEntity);
        $this->_updateProductUrlInIndex($productEntity, $url);
        return $url;
    }

    /**
     * @param Product $product
     * @param array $productChangedAttributes
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function _updateProductCategoryRelation(Product $product, array $productChangedAttributes)
    {
        if (array_key_exists('category_id', $productChangedAttributes)) {
            $this->_categoryProductService->updateRelation($product->id, $product->category_id);
        }
    }

    /**
     * @param Product $product
     * @param ProductSku $productSku
     * @param array $productChangedAttributes
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function _updateProductSkuCategoryRelation(
        Product $product,
        ProductSku $productSku,
        array $productChangedAttributes
    ) {
        if (array_key_exists('category_id', $productChangedAttributes)) {
            $this->_categoryProductSkuService->updateRelation($productSku->id, $product->category_id);
        }
    }

    /**
     * @param Product $product
     * @param ProductSkuCreateForm $productSkuCreateForm
     * @return ProductSku
     * @throws \Throwable
     */
    private function _createProductSku(Product $product, ProductSkuCreateForm $productSkuCreateForm): ProductSku
    {
        $productSku = new ProductSku();
        $productSku->product_id = $product->id;
        $productSku->setAttributes($productSkuCreateForm->getAttributes());
        if (empty($productSku->slug)) {
            if (empty($productSku->name)) {
                $productSku->slug = 'new';
            } else {
                $productSku->slug = $this->defineProductSkuSlug($productSku);
            }
        }
        $productSku->sort = $this->_productSkuRepository->getNextSortNumber($product->id);
        $this->_productSkuRepository->save($productSku);
        if ($productSku->slug == 'new') {
            $productSku->slug = $this->defineProductSkuSlug($productSku);
        }
        $this->_productSkuRepository->save($productSku);
        if ($product->isMainSkuSet()) {
            $this->duplicateEavAttributes($product->getMainSku(), $productSku);
        }
        if ($product->isCategorySet()) {
            $this->_categoryProductSkuService->updateRelation($productSku->id, $product->category_id);
        }
        $url = $this->_defineProductSkuUrl($product, $productSku);
        $this->_addProductSkuUrlToIndex($productSku, $url);
        return $productSku;
    }

    /**
     * @param Product $product
     * @param ProductSku $productSku
     * @param ProductSkuUpdateForm $productSkuInputForm
     * @param array $productChangedAttributes
     * @return array product sku changed attributes
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     * @throws \DmitriiKoziuk\yii2UrlIndex\exceptions\EntityUrlNotFoundException
     * @throws \DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException
     * @throws EntityNotFoundException
     */
    private function _updateProductSku(
        Product $product,
        ProductSku $productSku,
        ProductSkuUpdateForm $productSkuInputForm,
        array $productChangedAttributes
    ): array {
        $changedAttributes = [];
        $productSku->setAttributes($productSkuInputForm->getUpdatedAttributes());
        // Slug depends form name, but do not update slug if user change it itself.
        if ($productSku->isAttributeChanged('name')) {
            if (! $productSku->isAttributeChanged('slug')) {
                $productSku->slug = $this->defineProductSkuSlug($productSku);
            }
        }
        // Url depends form slug, but do not update url if user change it itself.
        if (
            $productSku->isAttributeChanged('slug') ||
            array_key_exists('slug', $productChangedAttributes) ||
            array_key_exists('url', $productChangedAttributes)
        ) {
            $changedAttributes['url'] = $this->updateProductSkuUrl($product, $productSku);
        }
        // user can change sell price only if strategy is static.
        if (ProductSku::SELL_PRICE_STRATEGY_STATIC == $productSku->sell_price_strategy) {
            // Change old_price field if is not changed and sell_price < old_price
            if ($productSku->isAttributeChanged('sell_price')) {
                if (
                    ! $productSku->isAttributeChanged('old_price') &&
                    $productSku->sell_price < $productSku->getOldAttribute('sell_price') &&
                    $productSku->old_price < $productSku->getOldAttribute('sell_price')
                ) {
                    $productSku->old_price = $productSku->getOldAttribute('sell_price');
                }
            }
            // Change price on site if changed sell price
            if (
                $productSku->isAttributeChanged('sell_price') ||
                $productSku->isAttributeChanged('currency_id', false)
            ) {
                $this->_defineProductSkuCustomerPrice($productSku);
            }
        } elseif ($productSku->isAttributeChanged('sell_price')) {
            $productSku->sell_price = $productSku->getOldAttribute('sell_price');
        }
        // change sell price if sell price strategy is changed to margin
        if (
            $productSku->isAttributeChanged('sell_price_strategy') &&
            ProductSku::SELL_PRICE_STRATEGY_MARGIN == $productSku->sell_price_strategy
        ) {
            $this->_defineProductSkuSellPrice($productSku);
            $this->_defineProductSkuCustomerPrice($productSku);
        }
        // change sell price if product type is changed
        if (
            ProductSku::SELL_PRICE_STRATEGY_MARGIN == $productSku->sell_price_strategy &&
            array_key_exists('type_id', $productChangedAttributes) &&
            ! empty($productChangedAttributes['type_id'])
        ) {
            $this->_defineProductSkuSellPrice($productSku);
            $this->_defineProductSkuCustomerPrice($productSku);
        }
        if (
            $productSku->isAttributeChanged('stock_status')
        ) {
            $this->updateProductSkuStatus($productSku);
        }
        $changedAttributes = array_merge($changedAttributes, $productSku->getDirtyAttributes());
        $this->_productSkuRepository->save($productSku);
        return $changedAttributes;
    }

    public function updateProductSkuUrl(Product $productEntity, ProductSku $productSkuEntity): string
    {
        $url = $this->_defineProductSkuUrl($productEntity, $productSkuEntity);
        $this->_updateProductSkuUrlInIndex($productSkuEntity, $url);
        return $url;
    }

    private function defineProductSlug($string): string
    {
        return UrlHelper::slugFromString($string);
    }

    private function defineProductSkuSlug(ProductSku $productSku): string
    {
        if (empty($productSku->name)) {
            return (string) $productSku->id;
        }
        return UrlHelper::slugFromString($productSku->name);
    }

    /**
     * @param Product $product
     * @return string
     * @throws EntityNotFoundException
     */
    private function _defineProductUrl(Product $product): string
    {
        $url = '';
        if (! empty($product->type_id)) {
            /** @var ProductType $productType */
            $productType = ProductType::find()->where(['id' => $product->type_id])->one();
            if (empty($productType)) {
                throw new EntityNotFoundException("Product type with id '{$product->type_id}' not found.");
            }
            if (! empty($productType->product_url_prefix)) {
                $url = $productType->product_url_prefix;
            }
        }
        $url .= $product->slug;
        return UrlHelper::slugFromString('/' . $url);
    }

    /**
     * @param Product $product
     * @param ProductSku $productSku
     * @return string
     * @throws EntityNotFoundException
     */
    private function _defineProductSkuUrl(Product $product, ProductSku $productSku): string
    {
        $url = $this->_defineProductUrl($product) . '/' . $productSku->slug;
        return UrlHelper::slugFromString('/' . $url);
    }

    private function _defineProductSkuSellPrice(ProductSku $productSkuRecord): void
    {
        if (
            $productSkuRecord->sell_price_strategy == ProductSku::SELL_PRICE_STRATEGY_MARGIN &&
            ! empty($productSkuRecord->getTypeID())
        ) {
            /** @var SupplierProductSkuData[] $allSupplierDataList */
            $allSupplierDataList = $this->_supplierService->getAllProductSkuSuppliers($productSkuRecord->id);
            // suppliers with the same currency as product sku
            $actualSupplierList = [];
            foreach ($allSupplierDataList as $supplierProductSkuData) {
                if ($supplierProductSkuData->getCurrencyId() == $productSkuRecord->currency_id) {
                    $actualSupplierList[] = $supplierProductSkuData;
                }
            }
            if (! empty($actualSupplierList)) {
                $productTypeData = $this->_productTypeService->getProductTypeById($productSkuRecord->getTypeID());
                $productTypeMarginDataList = $this->_productTypeMarginService
                    ->getProductTypeMargins($productSkuRecord->getTypeID());
                $marginData = $productTypeMarginDataList[ $productSkuRecord->currency_id ] ?? null;
                if (! empty($marginData)) {
                    $supplierPurchasePrice = $this->_defineSupplierPurchasePrice(
                        $productTypeData,
                        $actualSupplierList
                    );
                    if (! empty($supplierPurchasePrice)) {
                        $this->_defineProductSkuMarginSellPrice(
                            $productSkuRecord,
                            $marginData,
                            $supplierPurchasePrice
                        );
                    }
                }
            }
        }
    }

    /**
     * @param ProductTypeData $productTypeData
     * @param SupplierProductSkuData[] $supplierProductSkuDataList
     * @return float|null
     */
    private function _defineSupplierPurchasePrice(
        ProductTypeData $productTypeData,
        array $supplierProductSkuDataList
    ): ?float {
        switch ($productTypeData->getMarginStrategy()) {
            case ProductType::MARGIN_STRATEGY_USE_AVERAGE_SUPPLIER_PURCHASE_PRICE:
                $purchasePrice = $this->_getSupplierAveragePurchasePrice($supplierProductSkuDataList);
                break;
            case ProductType::MARGIN_STRATEGY_USE_LOWER_SUPPLIER_PURCHASE_PRICE:
                $purchasePrice = $this->_getSupplierLowerPurchasePrice($supplierProductSkuDataList);
                break;
            case ProductType::MARGIN_STRATEGY_USE_HIGHEST_SUPPLIER_PURCHASE_PRICE:
                $purchasePrice = $this->_getSupplierHighestPurchasePrice($supplierProductSkuDataList);
                break;
            default:
                $purchasePrice = null;
                break;
        }
        return $purchasePrice;
    }

    /**
     * @param SupplierProductSkuData[] $supplierProductSkuDataList
     * @return float|null
     */
    private function _getSupplierAveragePurchasePrice(
        array $supplierProductSkuDataList
    ): ?float {
        $purchasePrice = null;
        foreach ($supplierProductSkuDataList as $supplierProductSkuData) {
            if (empty($purchasePrice)) {
                $purchasePrice = $supplierProductSkuData->getPurchasePrice();
            } else {
                $purchasePrice += (float) $supplierProductSkuData->getPurchasePrice();
            }
        }
        $purchasePrice = $purchasePrice / count($supplierProductSkuDataList);
        return $purchasePrice;
    }

    /**
     * @param SupplierProductSkuData[] $supplierProductSkuDataList
     * @return float|null
     */
    private function _getSupplierLowerPurchasePrice(
        array $supplierProductSkuDataList
    ): ?float {
        $purchasePrice = null;
        foreach ($supplierProductSkuDataList as $supplierProductSkuData) {
            if (empty($purchasePrice)) {
                $purchasePrice = (float) $supplierProductSkuData->getPurchasePrice();
            } elseif ($purchasePrice > (float) $supplierProductSkuData->getPurchasePrice()) {
                $purchasePrice = (float) $supplierProductSkuData->getPurchasePrice();
            }
        }
        return $purchasePrice;
    }

    /**
     * @param SupplierProductSkuData[] $supplierProductSkuDataList
     * @return float|null
     */
    private function _getSupplierHighestPurchasePrice(
        array $supplierProductSkuDataList
    ): ?float {
        $purchasePrice = null;
        foreach ($supplierProductSkuDataList as $supplierProductSkuData) {
            if (empty($purchasePrice)) {
                $purchasePrice = (float) $supplierProductSkuData->getPurchasePrice();
            } elseif ($purchasePrice < (float) $supplierProductSkuData->getPurchasePrice()) {
                $purchasePrice = (float) $supplierProductSkuData->getPurchasePrice();
            }
        }
        return $purchasePrice;
    }

    private function _defineProductSkuMarginSellPrice(
        ProductSku $productSku,
        ProductTypeMarginData $productTypeMarginData,
        float $purchasePrice = null
    ): void {
        if (! empty($purchasePrice)) {
            switch ($productTypeMarginData->getMarginType()) {
                case ProductTypeMargin::MARGIN_TYPE_SUM:
                    $productSku->sell_price = $purchasePrice + $productTypeMarginData->getMarginValue();
                    break;
                case ProductTypeMargin::MARGIN_TYPE_PERCENT:
                    $sellPricePercent = ($purchasePrice / 100) * $productTypeMarginData->getMarginValue();
                    $productSku->sell_price = $purchasePrice + $sellPricePercent;
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param ProductSku $productSku
     */
    private function _defineProductSkuCustomerPrice(ProductSku $productSku): void
    {
        if ($productSku->isCurrencySet()) {
            $currencyData = $this->_currencyService->getCurrencyById((int) $productSku->currency_id);
            $productSku->customer_price = (int) ($productSku->sell_price * $currencyData->getRate());
        }
    }

    private function _addProductUrlToIndex(Product $product, string $url): void
    {
        $this->_urlIndexService->addUrl(new UrlCreateForm([
            'url' => $url,
            'module_name' => ShopModule::ID,
            'controller_name' => Product::FRONTEND_CONTROLLER_NAME,
            'action_name' => Product::FRONTEND_ACTION_NAME,
            'entity_id' => (string) $product->id,
        ]));
    }

    private function _addProductSkuUrlToIndex(ProductSku $productSku, string $url)
    {
        $this->_urlIndexService->addUrl(new UrlCreateForm([
            'url' => $url,
            'module_name' => ShopModule::ID,
            'controller_name' => ProductSku::FRONTEND_CONTROLLER_NAME,
            'action_name' => ProductSku::FRONTEND_ACTION_NAME,
            'entity_id' => (string) $productSku->id,
        ]));
    }

    /**
     * @param Product $product
     * @param string $url
     * @throws \DmitriiKoziuk\yii2UrlIndex\exceptions\EntityUrlNotFoundException
     * @throws \DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException
     */
    private function _updateProductUrlInIndex(Product $product, string $url): void
    {
        $this->_urlIndexService->updateEntityUrl(new UpdateEntityUrlForm([
            'url' => $url,
            'module_name' => ShopModule::ID,
            'controller_name' => Product::FRONTEND_CONTROLLER_NAME,
            'action_name' => Product::FRONTEND_ACTION_NAME,
            'entity_id' => (string) $product->id,
        ]));
    }

    /**
     * @param ProductSku $productSku
     * @param string $url
     * @throws \DmitriiKoziuk\yii2UrlIndex\exceptions\EntityUrlNotFoundException
     * @throws \DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException
     */
    private function _updateProductSkuUrlInIndex(ProductSku $productSku, string $url): void
    {
        $this->_urlIndexService->updateEntityUrl(new UpdateEntityUrlForm([
            'url' => $url,
            'module_name' => ShopModule::ID,
            'controller_name' => ProductSku::FRONTEND_CONTROLLER_NAME,
            'action_name' => ProductSku::FRONTEND_ACTION_NAME,
            'entity_id' => (string) $productSku->id,
        ]));
    }

    /**
     * @param ProductSku $source
     * @param ProductSku $destination
     * @throws \Exception
     */
    private function duplicateEavAttributes(ProductSku $source, ProductSku $destination): void
    {
        $this->_duplicateEavAttributes($source->eavVarcharValues, $destination);
        $this->_duplicateEavAttributes($source->eavDoubleValues, $destination);
        $this->_duplicateEavAttributes($source->eavTextValues, $destination);
    }

    /**
     * @param EavValueVarcharEntity[]|EavValueDoubleEntity[]|EavValueTextEntity[] $values
     * @param ProductSku $destination
     * @throws Exception
     */
    private function _duplicateEavAttributes(array $values, ProductSku $destination): void
    {
        foreach ($values as $value) {
            $eavAttribute = $value->eavAttribute;
            $this->productSkuEavAttributesService->createRelation(
                $eavAttribute->storage_type,
                $destination->id,
                $value->id
            );
        }
    }

    private function moveProductSkuToCategoriesEnd(ProductSku $productSku)
    {
        $category = $productSku->product->category;
        if (! empty($category)) {
            $this->categoryProductSkuRepository->updateSort(
                $category->id,
                $productSku->id,
                $this->categoryProductSkuRepository->getMaxSort($category->id)
            );

            if (! empty($category->parentList)) {
                foreach ($category->parentList as $parentCategory) {
                    $this->categoryProductSkuRepository->updateSort(
                        $parentCategory->id,
                        $productSku->id,
                        $this->categoryProductSkuRepository->getMaxSort($parentCategory->id)
                    );
                }
            }
        }
    }

    private function moveProductSkuToCategoriesStart(ProductSku $productSku)
    {
        $category = $productSku->product->category;
        if (! empty($category)) {
            $this->categoryProductSkuRepository->updateSort(
                $category->id,
                $productSku->id,
                1
            );

            if (! empty($category->parentList)) {
                foreach ($category->parentList as $parentCategory) {
                    $this->categoryProductSkuRepository->updateSort(
                        $parentCategory->id,
                        $productSku->id,
                        1
                    );
                }
            }
        }
    }

    private function updateProductSkuStatus(ProductSku $productSku): void
    {
        switch ($productSku->stock_status) {
            case ProductSku::STOCK_OUT:
                if (ProductSku::STOCK_STATUS_DELETED == $productSku->getOldAttribute('stock_status')) {
                    $this->createProductSkuRelationWithCategories($productSku);
                    $this->moveProductSkuToCategoriesEnd($productSku);
                } else {
                    $this->moveProductSkuToCategoriesEnd($productSku);
                }
                break;
            case ProductSku::STOCK_IN:
                if (ProductSku::STOCK_STATUS_DELETED == $productSku->getOldAttribute('stock_status')) {
                    $this->createProductSkuRelationWithCategories($productSku);
                } else {
                    $this->moveProductSkuToCategoriesStart($productSku);
                }
                break;
            case ProductSku::STOCK_STATUS_DELETED:
                $this->deleteProductSkuRelationWithCategories($productSku);
                break;
            case ProductSku::STOCK_AWAIT:
                if (ProductSku::STOCK_STATUS_DELETED == $productSku->getOldAttribute('stock_status')) {
                    $this->createProductSkuRelationWithCategories($productSku);
                    $this->moveProductSkuToCategoriesStart($productSku);
                }
                break;
            default :
                break;
        }
    }

    private function createProductSkuRelationWithCategories(ProductSku $productSku): void
    {
        if ($productSku->product->isCategorySet()) {
            $category = $productSku->product->category;
            $this->categoryProductSkuRepository->createRelation(
                $category->id,
                $productSku->id
            );

            if (! empty($category->parentList)) {
                foreach ($category->parentList as $parentCategory) {
                    $this->categoryProductSkuRepository->createRelation(
                        $parentCategory->id,
                        $productSku->id
                    );
                }
            }
        }
    }

    private function deleteProductSkuRelationWithCategories(ProductSku $productSku): void
    {
        if ($productSku->product->isCategorySet()) {
            $category = $productSku->product->category;
            $this->categoryProductSkuRepository->deleteRelation(
                $category->id,
                $productSku->id
            );

            if (! empty($category->parentList)) {
                foreach ($category->parentList as $parentCategory) {
                    $this->categoryProductSkuRepository->deleteRelation(
                        $parentCategory->id,
                        $productSku->id
                    );
                }
            }
        }
    }
}
