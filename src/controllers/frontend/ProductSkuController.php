<?php
namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2Shop\services\product\ProductSeoService;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;

final class ProductSkuController extends Controller
{
    /**
     * @var ProductSkuRepository
     */
    private $productSkuRepository;

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
        ProductSkuRepository $productSkuRepository,
        ProductSeoService $productSeoService,
        FileService $fileService,
        FileWebHelper $fileWebHelper,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productSkuRepository = $productSkuRepository;
        $this->_productSeoService = $productSeoService;
        $this->_fileWebHelper = $fileWebHelper;
        $this->_fileService = $fileService;
    }

    /**
     * @param UrlUpdateForm $url
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(UrlUpdateForm $url)
    {
        try {
            $productSkuEntity = $this->productSkuRepository->getById((int) $url->entity_id);
            if (empty($productSkuEntity)) {
                throw new EntityNotFoundException();
            }
            $productSkuView = new ProductSkuView($productSkuEntity);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Page not found.')
            );
        }
        return $this->render('index', [
            'productSkuView' => $productSkuView,
            'fileWebHelper' => $this->_fileWebHelper,
            'productSeoService' => $this->_productSeoService,
        ]);
    }
}
