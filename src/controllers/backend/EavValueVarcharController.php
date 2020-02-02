<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharSeoValueEntity;
use DmitriiKoziuk\yii2Shop\entities\search\EavValueVarcharEntitySearch;
use DmitriiKoziuk\yii2Shop\forms\eav\EavVarcharSeoValuesCompositeForm;
use DmitriiKoziuk\yii2Shop\repositories\EavValueVarcharSeoValueRepository;
use DmitriiKoziuk\yii2Shop\services\eav\EavVarcharValueSeoService;

/**
 * EavValueVarcharController implements the CRUD actions for EavValueVarcharEntity model.
 */
class EavValueVarcharController extends Controller
{
    /**
     * @var EavValueVarcharSeoValueRepository
     */
    private $repository;

    private $varcharValueSeoService;

    public function __construct(
        $id,
        $module,
        EavValueVarcharSeoValueRepository $repository,
        EavVarcharValueSeoService $varcharValueSeoService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->varcharValueSeoService = $varcharValueSeoService;
    }

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
     * Lists all EavValueVarcharEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EavValueVarcharEntitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EavValueVarcharEntity model.
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
     * Creates a new EavValueVarcharEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EavValueVarcharEntity();

        if ($model->load(Yii::$app->request->post())) {
            $model->code = Inflector::slug($model->value);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EavValueVarcharEntity model.
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
     * Deletes an existing EavValueVarcharEntity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Display seo page
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSeo(int $id)
    {
        $valueEntity = $this->findModel($id);
        $seoValueForm = new EavVarcharSeoValuesCompositeForm();
        if (
            Yii::$app->request->isPost &&
            $seoValueForm->load(Yii::$app->request->post())
        ) {
            if ($seoValueForm->validate()) {
                $this->varcharValueSeoService->updateSeoValues($valueEntity, $seoValueForm);
            } else {
                return $this->render('seo', [
                    'valueEntity' => $valueEntity,
                    'seoValueForm' => $seoValueForm,
                ]);
            }
        }
        $seoValuesEntities = $this->repository->getSeoValues($id);
        $seoCodeGroups = $this->repository->getCodeGroups();
        foreach ($seoCodeGroups as $code) {
            if (! array_key_exists($code['code'], $seoValuesEntities)) {
                array_push($seoValuesEntities, new EavValueVarcharSeoValueEntity([
                    'code' => $code['code'],
                ]));
            }
        }
        $seoValueForm = new EavVarcharSeoValuesCompositeForm(['seoValues' => $seoValuesEntities]);
        return $this->render('seo', [
            'valueEntity' => $valueEntity,
            'seoValueForm' => $seoValueForm,
        ]);
    }

    /**
     * Finds the EavValueVarcharEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EavValueVarcharEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EavValueVarcharEntity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
