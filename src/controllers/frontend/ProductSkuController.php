<?php
namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;
use DmitriiKoziuk\yii2Shop\services\product\ProductTypeService;
use DmitriiKoziuk\yii2Shop\services\product\ProductSeoService;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

final class ProductSkuController extends Controller
{
    /**
     * @var ProductService
     */
    private $_productService;

    /**
     * @var ProductTypeService
     */
    private $_productTypeService;

    /**
     * @var ProductSeoService
     */
    private $_productSeoService;

    /**
     * @var FileService
     */
    private $_fileService;

    /**
     * @var FileWebHelper
     */
    private $_fileWebHelper;

    public function __construct(
        string $id,
        Module $module,
        ProductService $productService,
        ProductTypeService $productTypeService,
        ProductSeoService $productSeoService,
        FileService $fileService,
        FileWebHelper $fileWebHelper,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_productService = $productService;
        $this->_productTypeService = $productTypeService;
        $this->_productSeoService = $productSeoService;
        $this->_fileWebHelper = $fileWebHelper;
        $this->_fileService = $fileService;
    }

    /**
     * @param UrlData $urlData
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(UrlData $urlData)
    {
        try {
            $productSkuData = $this->_productService->getProductSkuById((int) $urlData->getEntityId());
            $productData = $this->_productService->getProductById($productSkuData->getProductId());
            $productTypeData = null;
            if (! empty($productData->getTypeId())) {
                $productTypeData = $this->_productTypeService->getProductTypeById($productData->getTypeId());
            }
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException(
                Yii::t(BaseModule::TRANSLATE, 'Page not found.')
            );
        }
        $images = $this->_fileService->getImages(
            ProductSku::FILE_ENTITY_NAME,
            $productSkuData->getId()
        );
        $mainImage = null;
        if (! empty($images)) {
            $mainImage = array_shift($images);
        }
        return $this->render('index', [
            'productSkuData' => $productSkuData,
            'productData' => $productData,
            'productTypeData' => $productTypeData,
            'images' => $images,
            'mainImage' => $mainImage,
            'fileWebHelper' => $this->_fileWebHelper,
            'productSeoService' => $this->_productSeoService,
        ]);
    }
}