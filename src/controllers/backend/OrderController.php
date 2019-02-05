<?php
namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use DmitriiKoziuk\yii2Shop\data\order\OrderSearchParams;
use DmitriiKoziuk\yii2Shop\services\order\OrderWebService;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;

final class OrderController extends Controller
{
    /**
     * @var OrderWebService
     */
    private $_orderWebService;

    private $_fileWebHelper;

    public function __construct(
        string $id,
        Module $module,
        OrderWebService $orderWebService,
        FileWebHelper $fileWebHelper,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_orderWebService = $orderWebService;
        $this->_fileWebHelper = $fileWebHelper;
    }

    public function actionIndex()
    {
        $searchParams = new OrderSearchParams();
        $searchParams->load(Yii::$app->requestedParams);
        $ordersData = $this->_orderWebService->searchOrders($searchParams);
        return $this->render('index', [
            'searchParams' => $searchParams,
            'orders' => $ordersData,
        ]);
    }

    public function actionView(int $id)
    {
        $orderData = $this->_orderWebService->getOrderById($id);
        return $this->render('view', [
            'orderData' => $orderData,
            'fileWebHelper' => $this->_fileWebHelper,
        ]);
    }
}