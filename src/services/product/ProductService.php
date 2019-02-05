<?php
namespace DmitriiKoziuk\yii2Shop\services\product;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlCreateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2CustomUrls\services\UrlService;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\forms\product\ProductInputForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductSkuInputForm;
use DmitriiKoziuk\yii2Shop\helpers\UrlHelper;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\data\ProductSkuData;
use DmitriiKoziuk\yii2Shop\repositories\ProductRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\repositories\CurrencyRepository;
use DmitriiKoziuk\yii2Shop\services\category\CategoryProductService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryProductSkuService;

class ProductService extends EntityActionService
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
     * @var CurrencyRepository
     */
    private $_currencyRepository;

    /**
     * @var UrlService
     */
    private $_urlService;

    /**
     * @var CategoryProductService
     */
    private $_categoryProductService;

    /**
     * @var CategoryProductSkuService
     */
    private $_categoryProductSkuService;

    public function __construct(
        ProductRepository $productRepository,
        ProductSkuRepository $_productSkuRepository,
        CurrencyRepository $currencyRepository,
        UrlService $urlService,
        CategoryProductService $categoryProductService,
        CategoryProductSkuService $categoryProductSkuService,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_productRepository = $productRepository;
        $this->_productSkuRepository = $_productSkuRepository;
        $this->_currencyRepository = $currencyRepository;
        $this->_urlService = $urlService;
        $this->_categoryProductService = $categoryProductService;
        $this->_categoryProductSkuService = $categoryProductSkuService;
    }

    /**
     * @param ProductInputForm $productInputForm
     * @param ProductSkuInputForm $productSkuInputForm
     * @return Product
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function create(
        ProductInputForm $productInputForm,
        ProductSkuInputForm $productSkuInputForm
    ): Product {
        $this->beginTransaction();
        try {
            $product = $this->_createProduct($productInputForm);
            $productSku = $this->_createProductSku($product, $productSkuInputForm);
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
     * @param ProductInputForm $productInputForm
     * @param ProductSkuInputForm[] $productSkuInputForms
     * @return Product
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function update(
        int $productId,
        ProductInputForm $productInputForm,
        array $productSkuInputForms
    ): Product {
        try {
            $this->beginTransaction();
            $product = $this->_productRepository->getById($productId);
            $productChangedAttributes = $this->_updateProduct($product, $productInputForm);
            $this->_updateProductCategoryRelation($product, $productChangedAttributes);
            foreach ($productSkuInputForms as $productSkuInputForm) {
                $productSku = $this->_productSkuRepository->getById($productSkuInputForm->id);
                $this->_updateProductSku($product, $productSku, $productSkuInputForm);
                $this->_updateProductSkuCategoryRelation($product, $productSku, $productChangedAttributes);
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
     * @param ProductSkuInputForm $productSkuInputForm
     * @return ProductSku
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function addSkuToProduct(
        Product $product,
        ProductSkuInputForm $productSkuInputForm
    ): ProductSku { //TODO change Product $product to ProductInputForm $productInputForm.
        try {
            $this->beginTransaction();
            $productSku = $this->_createProductSku($product, $productSkuInputForm);
            $this->commitTransaction();
            return $productSku; //TODO return ProductSkuInputForm with new product sku date.
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    public function getProductSkuById(int $productSkuId)
    {
        $sku = $this->_productSkuRepository->getById($productSkuId);
        return new ProductSkuData($sku);
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
     * @param ProductInputForm $productInputForm
     * @return Product
     * @throws EntityNotFoundException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    private function _createProduct(ProductInputForm $productInputForm): Product
    {
        $product = new Product();
        $product->setAttributes($productInputForm->getAttributes());
        $product->slug = $this->_defineSlug($product->name);
        $product->url = $this->_defineProductUrl($product);
        $this->_productRepository->save($product);
        $this->_urlService->addUrlToIndex(new UrlCreateForm([
            'url' => $product->url,
            'module_name' => ShopModule::ID,
            'controller_name' => ShopModule::PRODUCT_FRONTEND_CONTROLLER_NAME,
            'action_name' => ShopModule::PRODUCT_FRONTEND_ACTION_NAME,
            'entity_id' => (string) $product->id,
        ]));
        return $product;
    }

    /**
     * @param Product          $product
     * @param ProductInputForm $productInputForm
     * @return array product changed attributes
     * @throws \Throwable
     */
    private function _updateProduct(
        Product $product,
        ProductInputForm $productInputForm
    ): array {
        $product->setAttributes($productInputForm->getAttributes());
        if ($product->isAttributeChanged('name')) {
            if (! $product->isAttributeChanged('slug')) {
                $product->slug = $this->_defineSlug($product->name);
            } else {
                $product->slug = $this->_defineSlug($product->slug);
            }
        }
        if ($product->isAttributeChanged('slug')) {
            $product->url = $this->_defineProductUrl($product);
            $this->_urlService->updateUrlInIndex(new UrlUpdateForm([
                'url' => $product->url,
                'module_name' => ShopModule::ID,
                'controller_name' => ShopModule::PRODUCT_FRONTEND_CONTROLLER_NAME,
                'action_name' => ShopModule::PRODUCT_FRONTEND_ACTION_NAME,
                'entity_id' => (string) $product->id,
            ]));
        }
        $changedAttributes = $product->getDirtyAttributes();
        $this->_productRepository->save($product);
        return $changedAttributes;
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
     * @param ProductSkuInputForm $productSkuInputForm
     * @return ProductSku
     * @throws \Throwable
     */
    private function _createProductSku(Product $product, ProductSkuInputForm $productSkuInputForm): ProductSku
    {
        $productSku = new ProductSku();
        $productSku->product_id = $product->id;
        $productSku->setAttributes($productSkuInputForm->getAttributes());
        if (empty($productSku->slug)) {
            $productSku->slug = $this->_defineSlug($productSku->name);
        }
        $productSku->url = $this->_defineProductSkuUrl($product, $productSku);
        $productSku->sort = ProductSku::getNextSortNumber($product->id);
        $this->_productSkuRepository->save($productSku);
        $this->_urlService->addUrlToIndex(new UrlCreateForm([
            'url' => $productSku->url,
            'module_name' => ShopModule::ID,
            'controller_name' => ShopModule::PRODUCT_SKU_FRONTEND_CONTROLLER_NAME,
            'action_name' => ShopModule::PRODUCT_SKU_FRONTEND_ACTION_NAME,
            'entity_id' => (string) $productSku->id,
        ]));
        return $productSku;
    }

    /**
     * @param Product $product
     * @param ProductSku $productSku
     * @param ProductSkuInputForm $productSkuInputForm
     * @return array product sku changed attributes
     * @throws EntityNotFoundException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    private function _updateProductSku(
        Product $product,
        ProductSku $productSku,
        ProductSkuInputForm $productSkuInputForm
    ): array {
        // Slug depends form name, but do not update slug if user change it itself.
        $productSku->setAttributes($productSkuInputForm->getAttributes());
        if ($productSku->isAttributeChanged('name')) {
            if (! $productSku->isAttributeChanged('slug')) {
                $productSku->slug = $this->_defineSlug($productSku->name);
            }
        }
        // Url depends form slug, but do not update url if user change it itself.
        if ($productSku->isAttributeChanged('slug')) {
            if (! $productSku->isAttributeChanged('url')) {
                $productSku->url = $this->_defineProductSkuUrl($product, $productSku);
                $this->_urlService->updateUrlInIndex(new UrlUpdateForm([
                    'url' => $productSku->url,
                    'module_name' => ShopModule::ID,
                    'controller_name' => ShopModule::PRODUCT_SKU_FRONTEND_CONTROLLER_NAME,
                    'action_name' => ShopModule::PRODUCT_SKU_FRONTEND_ACTION_NAME,
                    'entity_id' => (string) $productSku->id,
                ]));
            }
        }
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
            $productSku->price_on_site = $this->_defineSitePrice($productSku);
        }
        $changedAttributes = $productSku->getDirtyAttributes();
        $this->_productSkuRepository->save($productSku);
        return $changedAttributes;
    }

    private function _defineSlug($string)
    {
        return UrlHelper::slugFromString($string);
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
                $url = $productType->product_url_prefix . '-';
            }
        }
        $url .= $product->slug;
        return UrlHelper::slugFromString('/' . $url);
    }

    private function _defineProductSkuUrl(Product $product, ProductSku $productSku): string
    {
        if (! empty($product->type) && ! empty($product->type->product_url_prefix)) {
            $url = $product->type->product_url_prefix . '-' . $product->name . '/' . $productSku->slug;
        } else {
            $url = $product->name . '/' . $productSku->slug;
        }
        return UrlHelper::slugFromString('/' . $url);
    }

    /**
     * @param ProductSku $productSku
     * @return float|int|string
     * @throws EntityNotFoundException
     */
    private function _defineSitePrice(ProductSku $productSku)
    {
        if (empty($productSku->currency_id)) {
            return $productSku->sell_price;
        } else {
            $currency = $this->_currencyRepository->getCurrencyById($productSku->currency_id);
            if (empty($currency)) {
                throw new EntityNotFoundException("Currency with id '{$productSku->currency_id}' not found.");
            }
            return $productSku->sell_price * $currency->rate;
        }
    }
}