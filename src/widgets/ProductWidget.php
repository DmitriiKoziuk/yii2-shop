<?php
namespace DmitriiKoziuk\yii2Shop\widgets;

use yii\base\Widget;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2FileManager\entities\File;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\services\product\ProductSearchService;

class ProductWidget extends Widget
{
    /**
     * @var ProductSearchParams
     */
    public $searchParams;

    /**
     * @var int
     */
    public $productPerPage = 20;

    /**
     * @var \DmitriiKoziuk\yii2Shop\entities\Product[]
     */
    private $_products;

    /**
     * @var \yii\data\Pagination
     */
    private $_pagination;

    /**
     * @var array
     */
    private $_productsImages;

    /**
     * @var File[]
     */
    private $_productMainImages;

    /**
     * @var FileWebHelper
     */
    private $_fileWebHelper;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        parent::init();
        /** @var ProductSearchService $productSearchService */
        $productSearchService = \Yii::$container->get(ProductSearchService::class);
        $dataProvider = $productSearchService->searchBy($this->searchParams, $this->productPerPage);
        $this->_products = $dataProvider->getModels();
        $this->_pagination = $dataProvider->getPagination();
        /** @var FileService _fileService */
        $fileService = \Yii::$container->get(FileService::class);
        /** @var FileWebHelper _fileWebHelper */
        $this->_fileWebHelper = \Yii::$container->get(FileWebHelper::class);
        foreach ($this->_products as $product) {
            $productMainSku = $product->getMainSku();
            $this->_productsImages[ $productMainSku->id ] = $fileService->getImages(
                ProductSku::FILE_ENTITY_NAME,
                $productMainSku->id
            );
            $this->_productMainImages[ $productMainSku->id ] = array_shift(
                $this->_productsImages[ $productMainSku->id ]
            );
        }
    }

    public function run()
    {
        return $this->render('products', [
            'products' => $this->_products,
            'pagination' => $this->_pagination,
            'productImages' => $this->_productsImages,
            'productMainImages' => $this->_productMainImages,
            'fileWebHelper' => $this->_fileWebHelper,
        ]);
    }
}