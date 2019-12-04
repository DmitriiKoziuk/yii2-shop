<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\services\category\CategoryService;
use DmitriiKoziuk\yii2Shop\services\product\ProductSearchService;
use DmitriiKoziuk\yii2Shop\services\product\ProductSkuSearchService;
use DmitriiKoziuk\yii2Shop\services\eav\EavService;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;

final class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    private $_categoryService;

    /**
     * @var ProductSearchService
     */
    private $productSearchService;

    /**
     * @var ProductSkuSearchService
     */
    private $productSkuSearchService;

    /**
     * @var EavService
     */
    private $eavService;

    public function __construct(
        string $id,
        Module $module,
        CategoryService $categoryService,
        ProductSearchService $productSearchService,
        ProductSkuSearchService $productSkuSearchService,
        EavService $eavService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_categoryService = $categoryService;
        $this->productSearchService = $productSearchService;
        $this->productSkuSearchService = $productSkuSearchService;
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
            $filteredAttributes = [];
            $facetedAttributes = [];
            $productDataProvider = null;
            if ($categoryData->isProductsShow()) {
                $filteredAttributes = $this->eavService->getFilteredAttributesWithValues($filterParams);
                $facetedAttributes = $this->eavService->getFacetedAttributesWithValues(
                    $categoryData->getId(),
                    $filteredAttributes
                );
                $productSearchParams = new ProductSearchParams([
                    'category_id' => $categoryData->getId(),
                    'stock_status' => [ProductSku::STOCK_IN, ProductSku::STOCK_AWAIT],
                ]);
                if (empty($filteredAttributes)) {
                    $productDataProvider = $this->productSearchService->searchBy(
                        $productSearchParams,
                        2,
                        $filteredAttributes
                    );
                } else {
                    $productDataProvider = $this->productSkuSearchService->searchBy(
                        $productSearchParams,
                        2,
                        $filteredAttributes
                    );
                }
            }
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
            'productDataProvider' => $productDataProvider,
        ]);
    }
}
