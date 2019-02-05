<?php

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Module;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\entities\search\CurrencySearch;
use DmitriiKoziuk\yii2Shop\forms\currency\CurrencyInputForm;
use DmitriiKoziuk\yii2Shop\services\currency\CurrencyService;

/**
 * CurrencyController implements the CRUD actions for Currency model.
 */
final class CurrencyController extends Controller
{
    /**
     * @var CurrencyService
     */
    private $_currencyService;

    public function __construct(
        string $id,
        Module $module,
        CurrencyService $currencyService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_currencyService = $currencyService;
    }

    /**
     * @inheritdoc
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
     * Lists all Currency models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CurrencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Currency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \Throwable
     */
    public function actionCreate()
    {
        $currency = new Currency();
        $currencyInputForm = new CurrencyInputForm();
        if (
            Yii::$app->request->isPost                           &&
            $currencyInputForm->load(Yii::$app->request->post()) &&
            $currencyInputForm->validate()
        ) {
            $currency = $this->_currencyService->create($currencyInputForm);
            return $this->redirect(['update', 'id' => $currency->id]);
        }
        return $this->render('create', [
            'currency' => $currency,
            'currencyInputForm' => $currencyInputForm,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function actionUpdate($id)
    {
        $currency = $this->findModel($id);
        $currencyInputForm = new CurrencyInputForm();
        if (
            Yii::$app->request->isPost &&
            $currencyInputForm->load(Yii::$app->request->post()) &&
            $currencyInputForm->validate()
        ) {
            $currency = $this->_currencyService->update($currency->id, $currencyInputForm);
            $currencyInputForm->setAttributes($currency->getAttributes());
        } else {
            $currencyInputForm->setAttributes($currency->getAttributes());
        }
        return $this->render('update', [
            'currency' => $currency,
            'currencyInputForm' => $currencyInputForm,
        ]);
    }

    public function actionDelete($id)
    {
        //TODO: action delete.
        return $this->redirect(['index']);
    }

    /**
     * Finds the Currency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Currency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Currency::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
