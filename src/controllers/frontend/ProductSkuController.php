<?php
namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

final class ProductSkuController extends Controller
{
    /**
     * @var ProductSkuRepository
     */
    private $_productSkuRepository;

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
        ProductSkuRepository $productSkuRepository,
        FileService $fileService,
        FileWebHelper $fileWebHelper,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_productSkuRepository = $productSkuRepository;
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
        $productSku = $this->_productSkuRepository->getById((int) $urlData->getEntityId());
        if (empty($productSku)) {
            throw new NotFoundHttpException('Page not found.');
        }
        $images = $this->_fileService->getImages(
            ProductSku::FILE_ENTITY_NAME,
            $productSku->id
        );
        $mainImage = array_shift($images);
        return $this->render('index', [
            'productSku' => $productSku,
            'images' => $images,
            'mainImage' => $mainImage,
            'fileWebHelper' => $this->_fileWebHelper,
        ]);
    }
}