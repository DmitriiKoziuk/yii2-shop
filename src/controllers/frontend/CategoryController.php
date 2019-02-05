<?php
namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;

use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2CustomUrls\services\UrlFilterService;

use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;

final class CategoryController extends Controller
{
    private $_categoryRepository;

    public function __construct(
        string $id,
        Module $module,
        CategoryRepository $categoryRepository,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_categoryRepository = $categoryRepository;
    }

    /**
     * @param UrlData $urlData
     * @param UrlFilterService $filterService
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(UrlData $urlData, UrlFilterService $filterService)
    {
        /** @var Category|null $category */
        $category = $this->_categoryRepository->getById((int) $urlData->entity_id);
        if (empty($category)) {
            throw new NotFoundHttpException('Page not found.');
        }
        $searchParams = new ProductSearchParams();
        $searchParams->category_id = $category->id;
        return $this->render('index', [
            'category' => $category,
            'searchParams' => $searchParams,
            'filterService' => $filterService,
        ]);
    }
}