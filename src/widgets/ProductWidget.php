<?php

namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\data\ProductData;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;
use DmitriiKoziuk\yii2Shop\services\product\ProductSearchService;
use DmitriiKoziuk\yii2Shop\services\product\ProductTypeService;

class ProductWidget extends Widget
{
    /**
     * @var ProductSearchParams
     */
    public $searchParams;

    /**
     * @var int
     */
    public $productPerPage = 2;

    /**
     * @var string
     */
    public $indexPageUrl;

    /**
     * @var EavAttributeEntity[]
     */
    public $filteredAttributes;

    /**
     * @var ProductData[]
     */
    private $_products = [];

    /**
     * @var \yii\data\Pagination
     */
    private $_pagination;

    /**
     * @var FileWebHelper
     */
    private $_fileWebHelper;

    /**
     * @throws EntityNotFoundException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        parent::init();
        /** @var ProductService $productService */
        $productService = \Yii::$container->get(ProductService::class);
        /** @var ProductSearchService $productSearchService */
        $productSearchService = \Yii::$container->get(ProductSearchService::class);
        /** @var ProductTypeService $productTypeService */
        $productTypeService = \Yii::$container->get(ProductTypeService::class);
        /** @var FileService $fileService */
        $fileService = \Yii::$container->get(FileService::class);
        /** @var FileWebHelper _fileWebHelper */
        $this->_fileWebHelper = \Yii::$container->get(FileWebHelper::class);

        $dataProvider = $productSearchService->searchBy(
            $this->searchParams,
            $this->productPerPage,
            ProductSearchService::SEARCH_PRODUCT,
            $this->filteredAttributes
        );
        $this->_products = $this->_productModelsToData($dataProvider->getModels());
        $this->_pagination = $dataProvider->getPagination();
        foreach ($this->_products as $product) {
            $product->mainSku = $productService->getProductSkuById($product->getMainSkuId());
            if (! empty($product->getTypeId())) {
                $product->type = $productTypeService->getProductTypeById($product->getTypeId());
            }
            $product->images = $fileService->getImages(
                ProductSku::FILE_ENTITY_NAME,
                $product->mainSku->getId()
            );
            $product->mainImage = array_shift($product->images);
        }
    }

    public function run()
    {
        return $this->render('products', [
            'products' => $this->_products,
            'pagination' => $this->_pagination,
            'fileWebHelper' => $this->_fileWebHelper,
            'indexPageUrl' => $this->indexPageUrl,
        ]);
    }

    /**
     * @param Product[] $models
     * @return ProductData[]
     */
    private function _productModelsToData(array $models): array
    {
        $list = [];
        foreach ($models as $model) {
            $list[] = new ProductData($model);
        }
        return $list;
    }
}