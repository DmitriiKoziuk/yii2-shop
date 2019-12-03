<?php

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\search\EavValueDoubleEntitySearch;

/**
 * EavValueDoubleController implements the CRUD actions for EavValueDoubleEntity model.
 */
class EavValueDoubleController extends Controller
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
     * Lists all EavValueDoubleEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EavValueDoubleEntitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EavValueDoubleEntity model.
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
     * Creates a new EavValueDoubleEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EavValueDoubleEntity();

        if ($model->load(Yii::$app->request->post())) {
            $a = (float) $model->value;
            $b = (int) $model->value;
            $p = 0.0001;
            if (($a - $b) < $p) {
                $model->code = (int) $model->value;
            } else {
                $model->code = $model->value;
            }
            /** @var EavAttributeEntity $attributeEntity */
            $attributeEntity = EavAttributeEntity::find()->where(['id' => $model->attribute_id])->one();
            if (! is_null($attributeEntity->defaultValueTypeUnit)) {
                $model->code .= $attributeEntity->defaultValueTypeUnit->code;
                $model->value_type_unit_id = $attributeEntity->defaultValueTypeUnit->id;
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EavValueDoubleEntity model.
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

    /**
     * Deletes an existing EavValueDoubleEntity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EavValueDoubleEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EavValueDoubleEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EavValueDoubleEntity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
