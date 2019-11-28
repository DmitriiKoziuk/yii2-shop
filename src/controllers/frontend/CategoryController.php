<?php

namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2Shop\services\category\CategoryService;
use DmitriiKoziuk\yii2Shop\services\eav\EavService;

final class CategoryController extends Controller
{
    private $_categoryService;

    /**
     * @var EavService
     */
    private $eavService;

    public function __construct(
        string $id,
        Module $module,
        CategoryService $categoryService,
        EavService $eavService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_categoryService = $categoryService;
        $this->eavService = $eavService;
    }

    /**
     * @param UrlUpdateForm $url
     * @param array $filterParams
     * @param array $getParams
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(UrlUpdateForm $url, array $getParams = null, array $filterParams = [])
    {
        try {
            $categoryData = $this->_categoryService->getCategoryById((int) $url->entity_id);
            $filteredAttributes = $this->eavService->getFilteredAttributesWithValues($filterParams);
            $facetedAttributes = $this->eavService->getFacetedAttributesWithValues($categoryData->getId(), $filteredAttributes);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Page not found.')
            );
        }
        return $this->render('index', [
            'categoryData' => $categoryData,
            'indexPageUrl' => $url->url,
            'getParams' => $getParams,
            'filterParams' => $filterParams,
            'facetedAttributes' => $facetedAttributes,
            'filteredAttributes' => $filteredAttributes,
        ]);
    }
}
