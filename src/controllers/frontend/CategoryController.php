<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\data\CategoryData;
use DmitriiKoziuk\yii2Shop\services\product\ProductSearchService;
use DmitriiKoziuk\yii2Shop\services\product\ProductSkuSearchService;
use DmitriiKoziuk\yii2Shop\services\eav\EavService;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\repositories\BrandRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;

final class CategoryController extends Controller
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ProductSearchService
     */
    private $productSearchService;

    /**
     * @var ProductSkuSearchService
     */
    private $productSkuSearchService;

    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * @var EavService
     */
    private $eavService;

    /**
     * @var ConfigService
     */
    private $configService;

    public function __construct(
        string $id,
        Module $module,
        CategoryRepository $categoryRepository,
        ProductSearchService $productSearchService,
        ProductSkuSearchService $productSkuSearchService,
        BrandRepository $brandRepository,
        EavService $eavService,
        ConfigService $configService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->categoryRepository = $categoryRepository;
        $this->productSearchService = $productSearchService;
        $this->productSkuSearchService = $productSkuSearchService;
        $this->brandRepository = $brandRepository;
        $this->eavService = $eavService;
        $this->configService = $configService;
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
            $categoryEntity = $this->categoryRepository->getById((int) $url->entity_id);
            if (empty($categoryEntity)) {
                Yii::error("Exist link with id '{$url->id}' to not existing category.", __METHOD__);
                throw new NotFoundHttpException(Yii::t('app', 'Category not found.'));
            }
            $categoryData = new CategoryData($categoryEntity);
            $filteredAttributes = [];
            $facetedAttributes = [];
            $brands = [];
            $productDataProvider = null;
            if ($categoryData->isProductsShow()) {
                $productOnPage = (int) $this->configService->getValue(ShopModule::getId(), 'productsOnCategoryPage');
                $filteredAttributes = $this->eavService->getFilteredAttributesWithValues($filterParams);
                $facetedAttributes = $this->eavService->getFacetedAttributesWithValues(
                    $categoryData->getId(),
                    $filteredAttributes,
                    $filterParams
                );
                $brands = $this->brandRepository->getFilteredBrands($categoryData->getId(), $filteredAttributes);
                $productSearchParams = new ProductSearchParams([
                    'categoryIDs' => [$categoryData->getId()],
                ]);
                if (empty($filterParams)) {
                    $productDataProvider = $this->productSearchService->searchBy(
                        $productSearchParams,
                        $productOnPage,
                        $filteredAttributes,
                        $filterParams
                    );
                } else {
                    $productDataProvider = $this->productSkuSearchService->searchBy(
                        $productSearchParams,
                        $productOnPage,
                        $filteredAttributes,
                        $filterParams
                    );
                }
            }
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Page not found.')
            );
        }
        $viewParams = [
            'categoryData' => $categoryData,
            'indexPageUrl' => $url->url,
            'getParams' => $getParams,
            'filterParams' => $filterParams,
            'facetedAttributes' => $facetedAttributes,
            'filteredAttributes' => $filteredAttributes,
            'productDataProvider' => $productDataProvider,
            'brands' => $brands,
        ];
        if (
            $categoryData->isTemplateNameSet() &&
            $this->isCategoryTemplateExist($categoryData->getTemplateName())
        ) {
            return $this->render($categoryData->getTemplateName(), $viewParams);
        }
        return $this->render('index', $viewParams);
    }

    private function isCategoryTemplateExist(string $templateName): bool
    {
        return file_exists($this->getViewPath() . '/' . $templateName . '.php');
    }
}
