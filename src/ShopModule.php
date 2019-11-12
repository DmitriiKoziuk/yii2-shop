<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop;

use yii\di\Container;
use yii\db\Connection;
use yii\web\Application as WebApp;
use yii\base\Application as BaseApp;
use yii\console\Application as ConsoleApp;
use yii\queue\cli\Queue;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;
use DmitriiKoziuk\yii2ModuleManager\ModuleManager;
use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;
use DmitriiKoziuk\yii2UserManager\UserManager;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;
use DmitriiKoziuk\yii2FileManager\FileManagerModule;
use DmitriiKoziuk\yii2FileManager\repositories\FileRepository;
use DmitriiKoziuk\yii2Shop\repositories\CurrencyRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductTypeRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductTypeMarginRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryClosureRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryProductRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryProductSkuRepository;
use DmitriiKoziuk\yii2Shop\repositories\CartRepository;
use DmitriiKoziuk\yii2Shop\repositories\CartProductRepository;
use DmitriiKoziuk\yii2Shop\repositories\CustomerRepository;
use DmitriiKoziuk\yii2Shop\repositories\OrderRepository;
use DmitriiKoziuk\yii2Shop\repositories\OrderStageLogRepository;
use DmitriiKoziuk\yii2Shop\repositories\SupplierRepository;
use DmitriiKoziuk\yii2Shop\repositories\SupplierProductSkuRepository;
use DmitriiKoziuk\yii2Shop\repositories\SupplierPriceRepository;
use DmitriiKoziuk\yii2Shop\repositories\BrandRepository;
use DmitriiKoziuk\yii2Shop\repositories\EavRepository;
use DmitriiKoziuk\yii2Shop\services\currency\CurrencyService;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;
use DmitriiKoziuk\yii2Shop\services\product\ProductTypeService;
use DmitriiKoziuk\yii2Shop\services\product\ProductMarginService;
use DmitriiKoziuk\yii2Shop\services\product\ProductSearchService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryProductService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryProductSkuService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryClosureService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryService;
use DmitriiKoziuk\yii2Shop\services\cart\CartService;
use DmitriiKoziuk\yii2Shop\services\cart\CartWebService;
use DmitriiKoziuk\yii2Shop\services\customer\CustomerWebService;
use DmitriiKoziuk\yii2Shop\services\order\OrderSearchService;
use DmitriiKoziuk\yii2Shop\services\order\OrderWebService;
use DmitriiKoziuk\yii2Shop\services\order\OrderStageLogService;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierService;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierPriceService;
use DmitriiKoziuk\yii2Shop\services\brand\BrandService;
use DmitriiKoziuk\yii2Shop\services\eav\ProductSkuEavAttributesService;
use DmitriiKoziuk\yii2Shop\services\eav\EavService;

final class ShopModule extends \yii\base\Module implements ModuleInterface
{
    const ID = 'dk-shop';

    const TRANSLATION = 'dk-shop';
    const TRANSLATION_PRODUCT = 'dk-shop-product';
    const TRANSLATION_PRODUCT_SKU = 'dk-shop-product-sku';
    const TRANSLATION_PRODUCT_TYPE = 'dk-shop-product-type';
    const TRANSLATION_PRODUCT_TYPE_MARGIN = 'dk-shop-product-type-margin';
    const TRANSLATION_CATEGORY = 'dk-shop-category';
    const TRANSLATION_CURRENCY = 'dk-shop-currency';
    const TRANSLATION_CART = 'dk-shop-cart';
    const TRANSLATION_ORDER = 'dk-shop-order';
    const TRANSLATION_SUPPLIER = 'dk-shop-supplier';
    const TRANSLATION_BRAND = 'dk-shop-brand';

    const PRODUCT_FRONTEND_CONTROLLER_NAME = 'product';
    const PRODUCT_FRONTEND_ACTION_NAME = 'index';
    const PRODUCT_SKU_FRONTEND_CONTROLLER_NAME = 'product-sku';
    const PRODUCT_SKU_FRONTEND_ACTION_NAME = 'index';

    /**
     * @var Container
     */
    public $diContainer;

    /**
     * @var Queue
     */
    public $queue;

    /**
     * @var Connection
     */
    public $dbConnection;

    /**
     * Overwrite this param if you backend app id is different from default.
     * @var string
     */
    public $backendAppId;

    /**
     * Overwrite this param if you backend app id is different from default.
     * @var string
     */
    public $frontendAppId;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        /** @var BaseApp $app */
        $app = $this->module;
        $this->initLocalProperties($app);
        $this->registerTranslation($app);
        $this->registerClassesToDIContainer($app);
    }

    public static function getId(): string
    {
        return self::ID;
    }

    public function getBackendMenuItems(): array
    {
        return ['label' => 'Shop', 'items' => [
            ['label' => 'Orders', 'url' => ['/' . $this::ID . '/order/index']],
            ['label' => 'Products', 'url' => ['/' . $this::ID . '/product/index']],
            ['label' => 'Product types', 'url' => ['/' . $this::ID . '/product-type/index']],
            ['label' => 'Categories', 'url' => ['/' . $this::ID . '/category/index']],
            ['label' => 'Currency', 'url' => ['/' . $this::ID . '/currency/index']],
            ['label' => 'Suppliers', 'url' => ['/' . $this::ID . '/supplier/index']],
            ['label' => 'Suppliers prices', 'url' => ['/' . $this::ID . '/supplier-price/index']],
            ['label' => 'Brands', 'url' => ['/' . $this::ID . '/brand/index']],
            ['label' => 'Eav value types', 'url' => ['/' . $this::ID . '/eav-value-type/index']],
            ['label' => 'Eav value type units', 'url' => ['/' . $this::ID . '/eav-value-type-unit/index']],
            ['label' => 'Eav Attributes', 'url' => ['/' . $this::ID . '/eav-attribute/index']],
            ['label' => 'Eav value double', 'url' => ['/' . $this::ID . '/eav-value-double/index']],
            ['label' => 'Eav value varchar', 'url' => ['/' . $this::ID . '/eav-value-varchar/index']],
            ['label' => 'Eav value text', 'url' => ['/' . $this::ID . '/eav-value-text/index']],
            ['label' => 'Product type attributes', 'url' => ['/' . $this::ID . '/product-type-attribute/index']],
        ]];
    }

    public static function requireOtherModulesToBeActive(): array
    {
        return [
            ModuleManager::class,
            ConfigManagerModule::class,
            UserManager::class,
            FileManagerModule::class,
            CustomUrlsModule::class,
        ];
    }

    /**
     * @param BaseApp $app
     * @throws \InvalidArgumentException
     */
    private function initLocalProperties(BaseApp $app)
    {
        if (empty($this->backendAppId)) {
            throw new \InvalidArgumentException('Property backendAppId not set.');
        }
        if ($app instanceof WebApp && $app->id == $this->backendAppId) {
            $this->controllerNamespace = __NAMESPACE__ . '\controllers\backend';
            $this->viewPath = '@DmitriiKoziuk/yii2Shop/views/backend';
            if (empty($this->queue) || ! ($this->queue instanceof Queue)) {
                throw new \InvalidArgumentException('Property queue not set.');
            }
        }
        if ($app instanceof WebApp && $app->id == $this->frontendAppId) {
            $this->controllerNamespace = __NAMESPACE__ . '\controllers\frontend';
            $this->viewPath = '@DmitriiKoziuk/yii2Shop/views/frontend';
            $urlManager = $app->getUrlManager();
            $urlManager->addRules([
                '/cart' => self::ID . '/cart/view',
                '/cart/update' => self::ID . '/cart/update',
                '/cart/checkout' => self::ID . '/cart/checkout',
                '/cart/thanks' => self::ID . '/cart/thanks',
                '/cart/add-product' => self::ID . '/cart/add-product',
                '/cart/remove-product' => self::ID . '/cart/remove-product',
            ]);
        }
        if ($app instanceof ConsoleApp) {
            array_push(
                $app->controllerMap['migrate']['migrationNamespaces'],
                __NAMESPACE__ . '\migrations'
            );
        }
    }

    /**
     * @param BaseApp $app
     */
    private function registerTranslation(BaseApp $app)
    {
        $translationData = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath'       => '@DmitriiKoziuk/yii2Shop/messages',
        ];
        $app->i18n->translations[self::ID] = $translationData;
        $app->i18n->translations[self::TRANSLATION_PRODUCT] = $translationData;
        $app->i18n->translations[self::TRANSLATION_PRODUCT_SKU] = $translationData;
        $app->i18n->translations[self::TRANSLATION_PRODUCT_TYPE] = $translationData;
        $app->i18n->translations[self::TRANSLATION_PRODUCT_TYPE_MARGIN] = $translationData;
        $app->i18n->translations[self::TRANSLATION_CATEGORY] = $translationData;
        $app->i18n->translations[self::TRANSLATION_CURRENCY] = $translationData;
        $app->i18n->translations[self::TRANSLATION_CART] = $translationData;
        $app->i18n->translations[self::TRANSLATION_ORDER] = $translationData;
        $app->i18n->translations[self::TRANSLATION_SUPPLIER] = $translationData;
        $app->i18n->translations[self::TRANSLATION_BRAND] = $translationData;
    }

    /**
     * @param BaseApp $app
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function registerClassesToDIContainer(BaseApp $app): void
    {
        $this->diContainer->setSingleton(ProductRepository::class, function () {
            return new ProductRepository();
        });
        $this->diContainer->setSingleton(ProductSkuRepository::class, function () {
            return new ProductSkuRepository();
        });
        $this->diContainer->setSingleton(ProductTypeRepository::class, function () {
            return new ProductTypeRepository();
        });
        $this->diContainer->setSingleton(ProductTypeMarginRepository::class, function () {
            return new ProductTypeMarginRepository();
        });
        $this->diContainer->setSingleton(CurrencyRepository::class, function () {
            return new CurrencyRepository();
        });
        $this->diContainer->setSingleton(CategoryRepository::class, function () {
            return new CategoryRepository();
        });
        $this->diContainer->setSingleton(CategoryClosureRepository::class, function () {
            return new CategoryClosureRepository();
        });
        $this->diContainer->setSingleton(CategoryProductRepository::class, function () {
            return new CategoryProductRepository();
        });
        $this->diContainer->setSingleton(CategoryProductSkuRepository::class, function () {
            return new CategoryProductSkuRepository();
        });
        $this->diContainer->setSingleton(CartRepository::class, function () {
            return new CartRepository();
        });
        $this->diContainer->setSingleton(CartProductRepository::class, function () {
            return new CartProductRepository();
        });
        $this->diContainer->setSingleton(CustomerRepository::class, function () {
            return new CustomerRepository();
        });
        $this->diContainer->setSingleton(OrderRepository::class, function () {
            return new OrderRepository();
        });
        $this->diContainer->setSingleton(OrderStageLogRepository::class, function () {
            return new OrderStageLogRepository();
        });
        $this->diContainer->setSingleton(SupplierRepository::class, function () {
            return new SupplierRepository();
        });
        $this->diContainer->setSingleton(SupplierProductSkuRepository::class, function () {
            return new SupplierProductSkuRepository();
        });
        $this->diContainer->setSingleton(SupplierPriceRepository::class, function () {
            return new SupplierPriceRepository();
        });
        $this->diContainer->setSingleton(BrandRepository::class, function () {
            return new BrandRepository();
        });
        $this->diContainer->setSingleton(EavRepository::class, function () {
            return new EavRepository();
        });

        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = $this->diContainer->get(CurrencyRepository::class);
        /** @var CategoryClosureService $categoryClosureService */
        $categoryClosureService = $this->diContainer->get(CategoryClosureService::class);
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->diContainer->get(CategoryRepository::class);
        /** @var CategoryClosureRepository $categoryClosureRepository */
        $categoryClosureRepository = $this->diContainer->get(CategoryClosureRepository::class);
        /** @var CategoryProductRepository $categoryProductRepository */
        $categoryProductRepository = $this->diContainer->get(CategoryProductRepository::class);
        /** @var CategoryProductSkuRepository $categoryProductSkuRepository */
        $categoryProductSkuRepository = $this->diContainer->get(CategoryProductSkuRepository::class);
        /** @var UrlIndexService $urlService */
        $urlService = $this->diContainer->get(UrlIndexService::class);
        /** @var ProductRepository $productRepository */
        $productRepository = $this->diContainer->get(ProductRepository::class);
        /** @var ProductSkuRepository $productSkuRepository */
        $productSkuRepository = $this->diContainer->get(ProductSkuRepository::class);
        /** @var ProductTypeRepository $productTypeRepository */
        $productTypeRepository = $this->diContainer->get(ProductTypeRepository::class);
        /** @var ProductTypeMarginRepository $productTypeMarginRepository */
        $productTypeMarginRepository = $this->diContainer->get(ProductTypeMarginRepository::class);
        /** @var CartRepository $cartRepository */
        $cartRepository = $this->diContainer->get(CartRepository::class);
        /** @var CartProductRepository $cartProductRepository */
        $cartProductRepository = $this->diContainer->get(CartProductRepository::class);
        /** @var CustomerRepository $customerRepository */
        $customerRepository = $this->diContainer->get(CustomerRepository::class);
        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->diContainer->get(OrderRepository::class);
        /** @var OrderStageLogRepository $orderStageLogRepository */
        $orderStageLogRepository = $this->diContainer->get(OrderStageLogRepository::class);
        /** @var FileRepository $fileRepository */
        $fileRepository = $this->diContainer->get(FileRepository::class);
        /** @var SupplierRepository $supplierRepository */
        $supplierRepository = $this->diContainer->get(SupplierRepository::class);
        /** @var SupplierProductSkuRepository $supplierProductSkuRepository */
        $supplierProductSkuRepository = $this->diContainer->get(SupplierProductSkuRepository::class);
        /** @var SupplierPriceRepository $supplierPriceRepository */
        $supplierPriceRepository = $this->diContainer->get(SupplierPriceRepository::class);
        /** @var BrandRepository $brandRepository */
        $brandRepository = $this->diContainer->get(BrandRepository::class);
        /** @var EavRepository $eavRepository */
        $eavRepository = $this->diContainer->get(EavRepository::class);

        $this->diContainer->setSingleton(
            CurrencyService::class,
            function () use (
                $currencyRepository,
                $app
            ) {
                return new CurrencyService(
                    $currencyRepository,
                    $this->queue,
                    $app->db
                );
            }
        );
        /** @var CurrencyService $currencyService */
        $currencyService = $this->diContainer->get(CurrencyService::class);
        $this->diContainer->setSingleton(
            CategoryClosureService::class,
            function () use ($categoryClosureRepository, $categoryRepository) {
                return new CategoryClosureService($categoryClosureRepository, $categoryRepository);
            }
        );
        $this->diContainer->setSingleton(
            CategoryService::class,
            function () use ($categoryRepository, $categoryClosureService, $urlService) {
                return new CategoryService(
                    $categoryRepository,
                    $categoryClosureService,
                    $urlService,
                    $this->dbConnection
                );
            }
        );
        $this->diContainer->setSingleton(
            CategoryProductService::class,
            function () use ($categoryRepository, $categoryProductRepository) {
                return new CategoryProductService(
                    $categoryRepository,
                    $categoryProductRepository,
                    $this->dbConnection
                );
            }
        );
        $this->diContainer->setSingleton(
            CategoryProductSkuService::class,
            function () use ($categoryRepository, $categoryProductSkuRepository) {
                return new CategoryProductSkuService(
                    $categoryRepository,
                    $categoryProductSkuRepository
                );
            }
        );
        $this->diContainer->setSingleton(
            ProductTypeService::class,
            function () use (
                $productTypeRepository,
                $app
            ) {
                return new ProductTypeService(
                    $productTypeRepository,
                    $this->queue,
                    $app->db
                );
            }
        );
        /** @var ProductTypeService $productTypeService */
        $productTypeService = $this->diContainer->get(ProductTypeService::class);
        $this->diContainer->setSingleton(
            ProductMarginService::class,
            function () use (
                $productTypeMarginRepository,
                $currencyService,
                $productTypeService,
                $app
            ) {
                return new ProductMarginService(
                    $productTypeMarginRepository,
                    $currencyService,
                    $productTypeService,
                    $this->queue,
                    $app->db
                );
            }
        );
        /** @var ProductMarginService $productMarginService */
        $productMarginService = $this->diContainer->get(ProductMarginService::class);
        $this->diContainer->setSingleton(
            SupplierService::class,
            function () use (
                $supplierRepository,
                $supplierProductSkuRepository,
                $currencyService,
                $app
            ) {
                return new SupplierService(
                    $supplierRepository,
                    $supplierProductSkuRepository,
                    $currencyService,
                    $this->queue,
                    $app->db
                );
            }
        );
        $this->diContainer->setSingleton(
            SupplierPriceService::class,
            function () use ($supplierPriceRepository) {
                return new SupplierPriceService(
                    $supplierPriceRepository,
                    $this->queue
                );
            }
        );
        /** @var SupplierService $supplierService */
        $supplierService = $this->diContainer->get(SupplierService::class);
        /** @var CategoryProductService $categoryProductService */
        $categoryProductService = $this->diContainer->get(CategoryProductService::class);
        /** @var CategoryProductSkuService $categoryProductSkuService */
        $categoryProductSkuService = $this->diContainer->get(CategoryProductSkuService::class);
        $this->diContainer->setSingleton(
            ProductService::class,
            function () use (
                $productRepository,
                $productSkuRepository,
                $productTypeService,
                $productMarginService,
                $supplierService,
                $currencyRepository,
                $urlService,
                $categoryProductService,
                $categoryProductSkuService,
                $currencyService
            ) {
                return new ProductService(
                    $productRepository,
                    $productSkuRepository,
                    $productTypeService,
                    $productMarginService,
                    $supplierService,
                    $urlService,
                    $categoryProductService,
                    $categoryProductSkuService,
                    $currencyService,
                    $this->dbConnection
                );
            }
        );
        $this->diContainer->setSingleton(ProductSearchService::class, function () {
            return new ProductSearchService();
        });
        $this->diContainer->setSingleton(
            CartService::class,
            function () use (
                $cartRepository,
                $cartProductRepository,
                $customerRepository,
                $orderRepository,
                $orderStageLogRepository,
                $productSkuRepository
            ) {
                return new CartService(
                    $cartRepository,
                    $cartProductRepository,
                    $customerRepository,
                    $orderRepository,
                    $orderStageLogRepository,
                    $productSkuRepository,
                    $this->dbConnection
                );
            }
        );
        $this->diContainer->setSingleton(
            CartWebService::class,
            function () use ($cartRepository, $cartProductRepository, $fileRepository) {
                return new CartWebService(
                    $cartRepository,
                    $cartProductRepository,
                    $fileRepository
                );
            }
        );
        /** @var CartWebService $cartWebService */
        $cartWebService = $this->diContainer->get(CartWebService::class);
        $this->diContainer->setSingleton(
            CustomerWebService::class,
            function () use ($customerRepository) {
                return new CustomerWebService($customerRepository);
            }
        );
        /** @var CustomerWebService $customerWebService */
        $customerWebService = $this->diContainer->get(CustomerWebService::class);
        $this->diContainer->setSingleton(OrderSearchService::class, function () {
            return new OrderSearchService();
        });
        $this->diContainer->setSingleton(
            OrderStageLogService::class,
            function () use ($orderStageLogRepository) {
                return new OrderStageLogService($orderStageLogRepository);
            }
        );
        /** @var OrderSearchService $orderSearchService */
        $orderSearchService = $this->diContainer->get(OrderSearchService::class);
        /** @var OrderStageLogService $orderStageLogService */
        $orderStageLogService = $this->diContainer->get(OrderStageLogService::class);
        $this->diContainer->setSingleton(
            OrderWebService::class,
            function () use (
                $orderSearchService,
                $cartWebService,
                $customerWebService,
                $orderRepository,
                $orderStageLogService
            ) {
                return new OrderWebService(
                    $orderSearchService,
                    $cartWebService,
                    $customerWebService,
                    $orderRepository,
                    $orderStageLogService
                );
            }
        );
        $this->diContainer->setSingleton(
            BrandService::class,
            function () use (
                $brandRepository,
                $app
            ) {
                return new BrandService(
                    $brandRepository,
                    $app->db
                );
            }
        );
        $this->diContainer->setSingleton(
            ProductSkuEavAttributesService::class,
            function () use (
                $app
            ) {
                return new ProductSkuEavAttributesService($app->db);
            }
        );
        $this->diContainer->setSingleton(EavService::class, function () use ($eavRepository) {
            return new EavService($eavRepository);
        });
    }
}