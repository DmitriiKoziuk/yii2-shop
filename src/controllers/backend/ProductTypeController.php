<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Module;
use DmitriiKoziuk\yii2Shop\entities\search\ProductTypeSearch;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\forms\product\ProductTypeInputForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductMarginUpdateForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductMarginCompositeUpdateForm;
use DmitriiKoziuk\yii2Shop\services\product\ProductTypeService;
use DmitriiKoziuk\yii2Shop\services\product\ProductMarginService;

/**
 * ProductTypeController implements the CRUD actions for ProductType model.
 */
final class ProductTypeController extends Controller
{
    /**
     * @var ProductTypeService
     */
    private $_productTypeService;

    /**
     * @var ProductMarginService
     */
    private $_productMarginService;

    public function __construct(
        string $id,
        Module $module,
        ProductTypeService $productTypeService,
        ProductMarginService $productMarginService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_productTypeService = $productTypeService;
        $this->_productMarginService = $productMarginService;
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
                    'delete' => ['POST', 'GET'],
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

    /**
     * @param int $id product type id
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateMargin(int $id)
    {
        $compositeForm = new ProductMarginCompositeUpdateForm();
        if (
            Yii::$app->request->isPost &&
            $compositeForm->load(Yii::$app->request->post((new ProductMarginUpdateForm())->formName()))
        ) {
            $updateData = $this->_productMarginService->updateMargins($id, $compositeForm);
        } else {
            $updateData = $this->_productMarginService->getDataForUpdate($id);
        }
        return $this->render('update-margin', [
            'updateData' => $updateData,
        ]);
    }

    public function actionDelete($id)
    {
        $this->_productTypeService->deleteProductType((int) $id);
        return $this->redirect(['index']);
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
