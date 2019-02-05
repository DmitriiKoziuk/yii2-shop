<?php

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Module;
use DmitriiKoziuk\yii2Shop\entities\search\ProductTypeSearch;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\services\product\ProductTypeService;
use DmitriiKoziuk\yii2Shop\forms\product\ProductTypeInputForm;

/**
 * ProductTypeController implements the CRUD actions for ProductType model.
 */
final class ProductTypeController extends Controller
{
    /**
     * @var ProductTypeService
     */
    private $_productTypeService;

    public function __construct(
        string $id,
        Module $module,
        ProductTypeService $productTypeService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_productTypeService = $productTypeService;
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
     * Lists all ProductType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductTypeSearch();
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
     * @return string|\yii\web\Response
     * @throws \Throwable
     */
    public function actionCreate()
    {
        $productTypeInputForm = new ProductTypeInputForm();
        $productType = new ProductType();
        if (
            Yii::$app->request->isPost                              &&
            $productTypeInputForm->load(Yii::$app->request->post()) &&
            $productTypeInputForm->validate()
        ) {
            $productType = $this->_productTypeService->create($productTypeInputForm);
            return $this->redirect(['update', 'id' => $productType->id]);
        }
        return $this->render('create', [
            'productType' => $productType,
            'productTypeInputForm' => $productTypeInputForm,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionUpdate($id)
    {
        $productTypeInputForm = new ProductTypeInputForm();
        $productType = $this->findModel($id);
        if (
            Yii::$app->request->isPost                              &&
            $productTypeInputForm->load(Yii::$app->request->post()) &&
            $productTypeInputForm->validate()
        ) {
            $productType = $this->_productTypeService->update($productType, $productTypeInputForm);
            $productTypeInputForm->setAttributes($productType->getAttributes());
        } else {
            $productTypeInputForm->setAttributes($productType->getAttributes());
        }
        return $this->render('update', [
            'productType' => $productType,
            'productTypeInputForm' => $productTypeInputForm,
        ]);
    }

    public function actionDelete($id)
    {
        //TODO: product type delete action
        return $this->redirect(['index']);
    }


    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMargin($id)
    {
        $productType = ProductType::findOne($id);
        if (empty($productType)) {
            throw new NotFoundHttpException("Product type with id {$id} not found.");
        }
        if (Yii::$app->request->isPost) {
            $updateData = Yii::$app->request->post('ProductTypeMargin');
            ProductTypeService::updateMargin($productType, $updateData);
        }
        $margins = ProductTypeService::getMarginsForUpdate($productType);
        return $this->render('update-margin', [
            'productType' => $productType,
            'margins'     => $margins,
        ]);
    }

    /**
     * Finds the ProductType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
