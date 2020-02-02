<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\data\CategoryData;
use DmitriiKoziuk\yii2Shop\services\eav\EavService;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\repositories\BrandRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;

final class CategoryController extends Controller
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductSkuRepository
     */
    private $productSkuRepository;

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
        ProductRepository $productRepository,
        ProductSkuRepository $productSkuRepository,
        BrandRepository $brandRepository,
        EavService $eavService,
        ConfigService $configService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->productSkuRepository = $productSkuRepository;
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
        $viewParams = [];
        try {
            $filteredBrand = null;
            if (isset($filterParams['brand'])) {
                $filteredBrand = $this->brandRepository->getByCode($filterParams['brand'][ array_key_first($filterParams['brand']) ]);
                if (empty($filteredBrand)) {
                    throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
                }
            }
            $pageNumber = Yii::$app->request->get('page');
            $categoryEntity = $this->categoryRepository->getById((int) $url->entity_id);
            if (empty($categoryEntity)) {
                Yii::error("Exist link with id '{$url->id}' to not existing category.", __METHOD__);
                throw new NotFoundHttpException(Yii::t('app', 'Category not found.'));
            }
            $categoryData = new CategoryData($categoryEntity);
            $productDataProvider = null;
            if ($categoryData->isProductsShow()) {
                $productOnPage = (int) $this->configService->getValue(ShopModule::getId(), 'productsOnCategoryPage');
                $filteredAttributes = $this->eavService->getFilteredAttributesWithValues($filterParams);
                $facetedAttributes = $this->eavService->getFacetedAttributesWithValues(
                    $categoryData->getId(),
                    $filteredAttributes,
                    $filterParams
                );
                $brands = $this->brandRepository->getBrands($categoryData->getId(), $filteredAttributes);
                $productSearchParams = new ProductSearchParams([
                    'categoryIDs' => [$categoryData->getId()],
                    'limit' => $productOnPage,
                    'offset' => empty($pageNumber) ? null : (int) (($pageNumber - 1) * $productOnPage),
                ]);
                if (empty($filterParams)) {
                    $searchResponse = $this->productRepository->search(
                        $productSearchParams
                    );
                } else {
                    $searchResponse = $this->productSkuRepository->search(
                        $productSearchParams,
                        $filteredAttributes,
                        $filterParams
                    );
                }

                $pagination = new Pagination(['totalCount' => $searchResponse->getTotalCount()]);

                $viewParams = [
                    'categoryData' => $categoryData,
                    'indexPageUrl' => $url->url,
                    'getParams' => $getParams,
                    'filterParams' => $filterParams,
                    'facetedAttributes' => $facetedAttributes ?? [],
                    'filteredAttributes' => $filteredAttributes ?? [],
                    'products' => $searchResponse->getItems(),
                    'pagination' => $pagination,
                    'brands' => $brands ?? [],
                    'filteredBrand' => $filteredBrand,
                ];
            }
        } catch (EntityNotFoundException $e) {
            Yii::error('Error', __METHOD__);
            throw new NotFoundHttpException(
                Yii::t('app', 'Page not found.')
            );
        }

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
