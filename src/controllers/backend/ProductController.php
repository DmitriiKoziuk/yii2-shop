<?php

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\base\Module;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2FileManager\repositories\FileRepository;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\entities\search\ProductSkuSearch;
use DmitriiKoziuk\yii2Shop\forms\product\ProductInputForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductSkuUpdateForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductSkuCreateForm;
use DmitriiKoziuk\yii2Shop\services\product\ProductService;
use DmitriiKoziuk\yii2Shop\services\supplier\SupplierService;
use DmitriiKoziuk\yii2Shop\services\brand\BrandService;
use DmitriiKoziuk\yii2Shop\services\eav\ProductSkuEavAttributesService;

/**
 * ProductController implements the CRUD actions for Product model.
 */
final class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    private $_productService;

    /**
     * @var SupplierService
     */
    private $_supplierService;

    /**
     * @var BrandService
     */
    private $_brandService;

    /**
     * @var FileRepository
     */
    private $_fileRepository;

    /**
     * @var FileWebHelper
     */
    private $_fileWebHelper;

    private $productSkuEavAttributesService;

    public function __construct(
        string $id,
        Module $module,
        ProductService $productService,
        SupplierService $supplierService,
        BrandService $brandService,
        FileRepository $fileRepository,
        FileWebHelper $fileWebHelper,
        ProductSkuEavAttributesService $updateProductSkuAttributesService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_productService = $productService;
        $this->_supplierService = $supplierService;
        $this->_brandService = $brandService;
        $this->_fileRepository = $fileRepository;
        $this->_fileWebHelper = $fileWebHelper;
        $this->productSkuEavAttributesService = $updateProductSkuAttributesService;
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
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $productSkuSearch = new ProductSkuSearch();
        $dataProvider = $productSkuSearch->by(Yii::$app->request->queryParams);
        $productTypes = ProductType::find()->all();
        $categories = Category::find()->all();
        $brands = $this->_brandService->getAllBrands();

        return $this->render('index', [
            'productSkuSearch' => $productSkuSearch,
            'dataProvider' => $dataProvider,
            'productTypes' => $productTypes,
            'categories' => $categories,
            'brands' => $brands,
            'fileRepository' => $this->_fileRepository,
            'fileWebHelper' => $this->_fileWebHelper,
        ]);
    }

    public function actionCreate()
    {
        $productInputForm = new ProductInputForm(['scenario' => ProductInputForm::SCENARIO_CREATE]);
        $productSkuCreateForm = new ProductSkuCreateForm();

        if (Yii::$app->request->isPost) {
            try {
                if (
                    $productInputForm->load(Yii::$app->request->post())    &&
                    $productSkuCreateForm->load(Yii::$app->request->post()) &&
                    $productInputForm->validate()                          &&
                    $productSkuCreateForm->validate()
                ) {
                    $product = $this->_productService
                        ->create($productInputForm, $productSkuCreateForm);
                    return $this->redirect(['update', 'id' => $product->id]);
                } else {
                    throw new \Exception('Form not valid.');
                }
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'productInputForm' => $productInputForm,
            'productSkuInputForm' => $productSkuCreateForm,
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
        $product = $this->findProductEntity($id);
        $categories = Category::find()->all();
        $productTypes = ProductType::find()->all();
        $currencyList = Currency::find()->all();
        $productInputForm = new ProductInputForm(['scenario' => ProductInputForm::SCENARIO_UPDATE]);
        $productSkuInputForms = [];
        if (
            Yii::$app->request->isPost &&
            $productInputForm->load(Yii::$app->request->post()) &&
            $productInputForm->validate()
        ) {
            $skusPostData = Yii::$app->request->post(
                (new ProductSkuUpdateForm())->formName()
            );
            $skuValidationError = false;
            foreach ($skusPostData as $key => $sku) {
                /** @var $productSkuInputForms ProductSkuUpdateForm[] */
                $productSkuInputForms[ $key ] = new ProductSkuUpdateForm();
                $productSkuInputForms[ $key ]->setAttributes($sku);
                if (! $productSkuInputForms[ $key ]->validate()) {
                    $skuValidationError = true;
                }
            }
            if ($skuValidationError) {
                throw new DataNotValidException('Sku not valid.');
            }
            $product = $this->_productService->update(
                (int) $product->id,
                $productInputForm,
                $productSkuInputForms
            );
            $productInputForm->setAttributes($product->getAttributes());
            foreach ($product->skus as $key => $sku) {
                $productSkuInputForms[ $key ] = new ProductSkuUpdateForm();
                $productSkuInputForms[ $key ]->setAttributes($sku->getAttributes());
                $productSkuInputForms[ $key ]->files = $this->_fileRepository->getEntityImages(
                    $sku::FILE_ENTITY_NAME,
                    $sku->id
                );
            }
        } else {
            $productInputForm->setAttributes($product->getAttributes());
            /** @var ProductSkuUpdateForm[] $productSkuInputForms */
            foreach ($product->skus as $key => $sku) {
                $productSkuInputForms[ $key ] = new ProductSkuUpdateForm();
                $productSkuInputForms[ $key ]->setAttributes($sku->getAttributes());
                $productSkuInputForms[ $key ]->files = $this->_fileRepository->getEntityImages(
                    $sku::FILE_ENTITY_NAME,
                    $sku->id
                );
            }
        }
        $productSkuIds = ArrayHelper::map($productSkuInputForms, 'id', 'id');
        $productSkusSuppliers = $this->_supplierService->getProductSkusSuppliers($productSkuIds);
        $brands = $this->_brandService->getAllBrands();
        if (Yii::$app->request->isPost && ! empty(Yii::$app->request->post('productSku'))) {
            $this->productSkuEavAttributesService->update(
                Yii::$app->request->post('productSku'),
                $product->type_id
            );
        }
        return $this->render('update', [
            'product' => $product,
            'categories' => $categories,
            'productTypes' => $productTypes,
            'currencyList' => $currencyList,
            'productInputForm' => $productInputForm,
            'productSkuInputForms' => $productSkuInputForms,
            'productSkusSuppliers' => $productSkusSuppliers,
            'brands' => $brands,
            'fileWebHelper' => $this->_fileWebHelper,
        ]);
    }

    /**
     * @param $product_id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionCreateSku($product_id)
    {
        $product = $this->findProductEntity($product_id);
        $productInputForm = new ProductInputForm(['scenario' => ProductInputForm::SCENARIO_UPDATE]);
        $productInputForm->setAttributes($product->getAttributes());
        $productSkuCreateForm = new ProductSkuCreateForm();

        if (
            Yii::$app->request->isPost &&
            $productSkuCreateForm->load(Yii::$app->request->post()) &&
            $productSkuCreateForm->validate()
        ) {
            try {
                $this->_productService->addNewSkuToProduct($product, $productSkuCreateForm);
                return $this->redirect(['update', 'id' => $product->id]);
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create-sku', [
            'productInputForm' => $productInputForm,
            'productSkuInputForm' => $productSkuCreateForm,
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionDelete($id)
    {
        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findProductEntity($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
