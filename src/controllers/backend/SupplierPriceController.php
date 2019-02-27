<?php

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\base\Module;
use yii\queue\Queue;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2FileManager\services\FileService;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2Shop\entities\SupplierPrice;
use DmitriiKoziuk\yii2Shop\entities\SupplierPriceSearch;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierService;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierPriceService;

/**
 * SupplierPriceController implements the CRUD actions for SupplierPrice model.
 */
class SupplierPriceController extends Controller
{
    /**
     * @var SupplierService
     */
    private $_supplierService;

    /**
     * @var SupplierPriceService
     */
    private $_supplierPriceService;

    /**
     * @var FileService
     */
    private $_fileService;

    /**
     * @var FileWebHelper
     */
    private $_fileWebHelper;

    /**
     * @var Queue
     */
    private $_queue;

    public function __construct(
        string $id,
        Module $module,
        SupplierService $supplierService,
        SupplierPriceService $supplierPriceService,
        FileService $fileService,
        FileWebHelper $fileWebHelper,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_supplierService = $supplierService;
        $this->_supplierPriceService = $supplierPriceService;
        $this->_fileService = $fileService;
        $this->_fileWebHelper = $fileWebHelper;
        $this->_queue = Yii::$app->dkShopQueue;
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
     * Lists all SupplierPrice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SupplierPriceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $files = [];
        /** @var SupplierPrice $model */
        foreach ($dataProvider->getModels() as $model) {
            $files[ $model->id ] = $this->_fileService->getAllFiles(
                SupplierPrice::FILE_ENTITY_NAME,
                $model->id
            );
        }
        $jobStatus = [];
        foreach ($dataProvider->getModels() as $model) {
            if (! empty($model->job_id)) {
                $jobStatus[ $model->id ] = $this->_queue->status($model->job_id);
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'files' => $files,
            'jobStatus' => $jobStatus,
        ]);
    }

    /**
     * @param int $supplier_id
     * Creates a new SupplierPrice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(int $supplier_id)
    {
        $model = new SupplierPrice();
        $model->supplier_id = $supplier_id;

        if ($model->save()) {
            return $this->redirect(['upload-price', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUploadPrice(int $id)
    {
        $supplierPriceData = $this->_supplierPriceService->getSupplierPriceById($id);
        if (empty($supplierPriceData)) {
            throw new NotFoundHttpException("Price with id '{$id}' not exist.");
        }
        $files = $this->_fileService
            ->getAllFiles(
                SupplierPrice::FILE_ENTITY_NAME,
                $supplierPriceData->getId()
            );
        $supplier = $this->_supplierService->getSupplierById($supplierPriceData->getSupplierId());
        if (empty($supplier)) {
            throw new NotFoundHttpException("Supplier with id '{$id}' not exist.");
        }
        return $this->render('upload-price', [
            'supplierPriceData' => $supplierPriceData,
            'files' => $files,
            'supplier' => $supplier,
            'fileWebHelper' => $this->_fileWebHelper,
        ]);
    }

    public function actionProcessPrice(int $id)
    {
        $this->_supplierPriceService->processSupplierPrice($id);
        return $this->redirect(['index']);
    }

    /**
     * Finds the SupplierPrice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SupplierPrice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SupplierPrice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
