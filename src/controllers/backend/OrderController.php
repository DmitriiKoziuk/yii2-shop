<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2UserManager\entities\User;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\Order;
use DmitriiKoziuk\yii2Shop\entities\OrderStageLog;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\entities\search\OrderSearch;
use DmitriiKoziuk\yii2Shop\forms\order\OrderUpdateStatusForm;
use DmitriiKoziuk\yii2Shop\repositories\OrderStageLogRepository;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @var FileWebHelper
     */
    private $fileWebHelper;

    /**
     * @var OrderStageLogRepository
     */
    private $orderStageLogRepository;

    public function __construct(
        $id,
        $module,
        ConfigService $configService,
        FileWebHelper $fileWebHelper,
        OrderStageLogRepository $orderStageLogRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->configService = $configService;
        $this->fileWebHelper = $fileWebHelper;
        $this->orderStageLogRepository = $orderStageLogRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $currencies = Currency::find()->indexBy('code')->all();
        $mainCurrency = $currencies[ $this->configService->getValue(ShopModule::ID, 'mainCurrencyCode') ];
        $users = User::find()
            ->indexBy('id')
            ->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currencies' => $currencies,
            'mainCurrency' => $mainCurrency,
            'users' => $users,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    public function actionView($id)
    {
        $orderUpdateStatusForm = new OrderUpdateStatusForm();
        $users = User::find()
            ->indexBy('id')
            ->all();

        if (
            Yii::$app->request->isPost &&
            $orderUpdateStatusForm->load(Yii::$app->request->post()) &&
            $orderUpdateStatusForm->validate()
        ) {
            $newOrderStageLog = new OrderStageLog();
            $newOrderStageLog->setAttributes($orderUpdateStatusForm->getAttributes());
            $newOrderStageLog->order_id = $id;
            $newOrderStageLog->user_id = Yii::$app->getUser()->getId();
            $this->orderStageLogRepository->save($newOrderStageLog);
            $orderUpdateStatusForm = new OrderUpdateStatusForm();
        }

        return $this->render('view', [
            'order' => $this->findModel($id),
            'fileWebHelper' => $this->fileWebHelper,
            'updateStatusForm' => $orderUpdateStatusForm,
            'users' => $users,
        ]);
    }

    public function actionToWork(int $id)
    {
        $newOrderStageLog = new OrderStageLog();
        $newOrderStageLog->stage_id = OrderStageLog::STATUS_IN_WORK;
        $newOrderStageLog->order_id = $id;
        $newOrderStageLog->user_id = Yii::$app->getUser()->getId();
        $this->orderStageLogRepository->save($newOrderStageLog);
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
