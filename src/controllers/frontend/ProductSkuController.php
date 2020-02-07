<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\GoneHttpException;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2Shop\services\product\ProductSeoService;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
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
    private $productSeoService;

    /**
     * @var FileService
     */
    private $fileService;

    public function __construct(
        string $id,
        Module $module,
        ProductSkuRepository $productSkuRepository,
        ProductSeoService $productSeoService,
        FileService $fileService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productSkuRepository = $productSkuRepository;
        $this->productSeoService = $productSeoService;
        $this->fileService = $fileService;
    }

    /**
     * @param UrlUpdateForm $url
     * @return string
     * @throws NotFoundHttpException
     * @throws GoneHttpException
     */
    public function actionIndex(UrlUpdateForm $url)
    {
        try {
            $productSkuEntity = $this->productSkuRepository->getById((int) $url->entity_id);
            if (empty($productSkuEntity)) {
                throw new EntityNotFoundException();
            }
            if (ProductSku::STOCK_STATUS_DELETED == $productSkuEntity->stock_status) {
                throw new GoneHttpException("Product deleted.");
            }
            $productSkuView = new ProductSkuView($productSkuEntity);
        } catch (EntityNotFoundException $e) {
            Yii::error("Exist link to non exist product. Link id '{$url->id}'", __METHOD__);
            throw new NotFoundHttpException(
                Yii::t('app', 'Page not found.')
            );
        }
        return $this->render('index', [
            'productSkuView' => $productSkuView,
            'productSeoService' => $this->productSeoService,
        ]);
    }
}
