<?php

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\search\ProductTypeAttributeEntitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductTypeAttributeController implements the CRUD actions for ProductTypeAttributeEntity model.
 */
class ProductTypeAttributeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProductTypeAttributeEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductTypeAttributeEntitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductTypeAttributeEntity model.
     * @param integer $product_type_id
     * @param integer $attribute_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($product_type_id, $attribute_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($product_type_id, $attribute_id),
        ]);
    }

    /**
     * Creates a new ProductTypeAttributeEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductTypeAttributeEntity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'product_type_id' => $model->product_type_id, 'attribute_id' => $model->attribute_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductTypeAttributeEntity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $product_type_id
     * @param integer $attribute_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($product_type_id, $attribute_id)
    {
        $model = $this->findModel($product_type_id, $attribute_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'product_type_id' => $model->product_type_id, 'attribute_id' => $model->attribute_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductTypeAttributeEntity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $product_type_id
     * @param integer $attribute_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($product_type_id, $attribute_id)
    {
        $this->findModel($product_type_id, $attribute_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductTypeAttributeEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $product_type_id
     * @param integer $attribute_id
     * @return ProductTypeAttributeEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($product_type_id, $attribute_id)
    {
        if (($model = ProductTypeAttributeEntity::findOne(['product_type_id' => $product_type_id, 'attribute_id' => $attribute_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
