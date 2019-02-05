<?php

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\entities\search\CategorySearch;
use DmitriiKoziuk\yii2Shop\forms\CategoryInputForm;
use DmitriiKoziuk\yii2Shop\services\category\CategoryService;
use DmitriiKoziuk\yii2Shop\helpers\CategoryHelper;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
final class CategoryController extends Controller
{
    private $_categoryService;

    public function __construct(string $id, Module $module, CategoryService $categoryService, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_categoryService = $categoryService;
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

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
        $categoryInputForm = new CategoryInputForm();
        $category = new Category();
        $categories = Category::find()->all();

        if (Yii::$app->request->isPost) {
            if ($categoryInputForm->load(Yii::$app->request->post()) && $categoryInputForm->validate()) {
                $category = $this->_categoryService->createCategory($categoryInputForm);
                return $this->redirect(['update', 'id' => $category->id]);
            }
        }

        return $this->render('create', [
            'categoryInputForm' => $categoryInputForm,
            'category' => $category,
            'categories'=> $categories,
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
        $category = $this->findModel($id);
        $categoryInputForm = new CategoryInputForm();
        $categories = Category::find()->all();

        if (Yii::$app->request->isPost) {
            if ($categoryInputForm->load(Yii::$app->request->post()) && $categoryInputForm->validate()) {
                $category = $this->_categoryService->updateCategory($category, $categoryInputForm);
                $categoryInputForm->setAttributes($category->getAttributes());
            }
        } else {
            $categoryInputForm->setAttributes($category->getAttributes());
        }

        return $this->render('update', [
            'categoryInputForm' => $categoryInputForm,
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    public function actionFullTree()
    {
        $categories = Category::find()->all();
        $treeList   = CategoryHelper::categoryTreeToList(
            CategoryHelper::createCategoryTree($categories)
        );
        return $this->render('full-tree', [
            'tree' => $treeList,
        ]);
    }

    public function actionDelete($id)
    {
        //TODO: delete category
        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
