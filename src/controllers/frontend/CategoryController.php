<?php
namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2CustomUrls\services\UrlFilterService;
use DmitriiKoziuk\yii2Shop\services\category\CategoryService;

final class CategoryController extends Controller
{
    private $_categoryService;

    public function __construct(
        string $id,
        Module $module,
        CategoryService $categoryService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_categoryService = $categoryService;
    }

    /**
     * @param UrlData $urlData
     * @param UrlFilterService $filterService
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(UrlData $urlData, UrlFilterService $filterService)
    {
        try {
            $categoryData = $this->_categoryService
                ->getCategoryById((int) $urlData->getEntityId());
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException(
                Yii::t(BaseModule::TRANSLATE, 'Page not found.')
            );
        }
        return $this->render('index', [
            'categoryData' => $categoryData,
            'filterService' => $filterService,
        ]);
    }
}