<?php
namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2Shop\entities\Supplier;
use DmitriiKoziuk\yii2Shop\entities\search\SupplierSearch;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierService;
use DmitriiKoziuk\yii2Shop\services\currency\CurrencyService;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;
use DmitriiKoziuk\yii2Shop\forms\supplier\SupplierProductSkuCompositeUpdateForm;

/**
 * SupplierController implements the CRUD actions for Supplier model.
 */
final class SupplierController extends Controller
{
    /**
     * @var SupplierService
     */
    private $_supplierService;

    /**
     * @var CurrencyService
     */
    private $_currencyService;

    /**
     * @var ProductService
     */
    private $_productService;

    public function __construct(
        string $id,
        Module $module,
        SupplierService $supplierService,
        CurrencyService $currencyService,
        ProductService $productService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_supplierService = $supplierService;
        $this->_currencyService = $currencyService;
        $this->_productService = $productService;
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
     * Lists all Supplier models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SupplierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Supplier model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Supplier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Supplier();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Supplier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAddProductSku(int $product_sku_id)
    {
        if (Yii::$app->request->isPost) {
            $suppliers = Yii::$app->request->post('selected');
            if (! empty($suppliers)) {
                $this->_supplierService->addProductSkuToSuppliers($suppliers, $product_sku_id);
            }
            $this->redirect(['supplier/update-product-sku-data', 'product_sku_id' => $product_sku_id]);
        }
        $suppliers = $this->_supplierService->getNonSelectedSuppliers($product_sku_id);
        $productSkuData = $this->_productService->getProductSkuById($product_sku_id);
        return $this->render('add-product-sku', [
            'suppliers' => $suppliers,
            'productSkuData' => $productSkuData,
        ]);
    }

    public function actionUpdateProductSkuData(int $product_sku_id)
    {
        if (Yii::$app->request->isPost) {
            $compositeForm = new SupplierProductSkuCompositeUpdateForm();
            if (
                $compositeForm->load(Yii::$app->request->post('SupplierProductSkuUpdateForm')) &&
                $compositeForm->validate()
            ) {
                $this->_supplierService->updateSupplierProductSkuData($compositeForm);
            }
        }
        $suppliersProductSkuData = $this->_supplierService->getAllProductSkuSuppliers($product_sku_id);
        $allCurrencies = $this->_currencyService->getAllCurrencies();
        $productSkuData = $this->_productService->getProductSkuById($product_sku_id);
        return $this->render('update-product-sku-data', [
            'suppliersProductSkuData' => $suppliersProductSkuData,
            'allCurrencies' => $allCurrencies,
            'productSkuData' => $productSkuData,
        ]);
    }

    /**
     * Finds the Supplier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Supplier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Supplier::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
