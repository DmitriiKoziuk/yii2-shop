<?php
namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
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
     * @param UrlUpdateForm $url
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(UrlUpdateForm $url)
    {
        try {
            $categoryData = $this->_categoryService
                ->getCategoryById((int) $url->entity_id);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Page not found.')
            );
        }
        return $this->render('index', [
            'categoryData' => $categoryData,
        ]);
    }
}