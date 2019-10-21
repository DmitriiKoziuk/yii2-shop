<?php
namespace DmitriiKoziuk\yii2Shop\services\category;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\traits\ModelValidatorTrait;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\forms\CategoryInputForm;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\data\CategoryData;
use DmitriiKoziuk\yii2Shop\helpers\UrlHelper;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;
use DmitriiKoziuk\yii2Shop\exceptions\category\CategoryInputFormNotValid;

final class CategoryService extends DBActionService
{
    use ModelValidatorTrait;

    /**
     * @var CategoryRepository
     */
    private $_categoryRepository;

    /**
     * @var CategoryClosureService
     */
    private $_categoryClosureService;

    /**
     * @var UrlIndexService
     */
    private $_urlIndexService;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryClosureService $categoryClosureService,
        UrlIndexService $urlIndexService,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_categoryClosureService = $categoryClosureService;
        $this->_urlIndexService = $urlIndexService;
        $this->_categoryRepository = $categoryRepository;
    }

    /**
     * @param CategoryInputForm $categoryInputForm
     * @return \DmitriiKoziuk\yii2Shop\entities\Category
     * @throws \Throwable
     */
    public function createCategory(CategoryInputForm $categoryInputForm): Category
    {
        $this->validateModels(
            [$categoryInputForm],
            new CategoryInputFormNotValid('Category input form not valid.')
        );

        $this->beginTransaction();
        try {
            $category = new Category();
            $category->setAttributes($categoryInputForm->getAttributes());
            $category->slug = $this->defineSlug($category->name);
            $category->url = $this->defineUrl($category);
            $this->_categoryRepository->save($category);
            $this->_categoryClosureService->updateRelations($category);
            $this->_addCategoryUrlToIndex($category);
            $this->commitTransaction();
            return $category;
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param Category $category
     * @param CategoryInputForm $categoryInputForm
     * @return Category
     * @throws \Throwable
     */
    public function updateCategory(Category $category, CategoryInputForm $categoryInputForm)
    {
        $this->beginTransaction();
        try {
            $category->setAttributes($categoryInputForm->getChangedAttributes());
            $changedAttributesList = $category->getDirtyAttributes();
            if (isset($changedAttributesList['slug'])) {
                $category->slug = $this->defineSlug($category->slug);
                $category->url  = $this->defineUrl($category);
            }
            $this->_categoryRepository->save($category);
            if (isset($changedAttributesList['slug'])) {
                $this->_updateCategoryUrlInIndex($category);
                $this->_updateChildrenUrl($category);
            }
            if (isset($changedAttributesList['parent_id'])) {
                $category->url = $this->defineUrl($category);
                $this->_categoryRepository->save($category);
                $this->_updateChildrenUrl($category);
                $this->_categoryClosureService->updateRelations($category);
            }
            $this->commitTransaction();
            return $category;
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    public function deleteCategory()
    {
        //TODO make delete method for category.
    }

    public function getCategoryById(int $categoryId): CategoryData
    {
        $categoryRecord = $this->_categoryRepository->getById($categoryId);
        if (empty($categoryRecord)) {
            throw new EntityNotFoundException("Can't find category with id '{$categoryId}'");
        }
        return new CategoryData($categoryRecord);
    }

    private function defineSlug($string): string
    {
        return UrlHelper::slugFromString($string);
    }

    /**
     * @param Category $category
     * @return string
     */
    public function defineUrl(Category $category): string
    {
        if (empty($category->parent_id)) {
            $url = $category->slug;
        } else {
            $url = $category->parent->url . '/' . $category->slug;
        }
        return UrlHelper::slugFromString( '/' . $url);
    }

    /**
     * @param Category $category
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     */
    private function _updateChildrenUrl(Category $category)
    {
        foreach ($category->directChildren as $child) {
            $child->url = $this->defineUrl($child);
            $this->_categoryRepository->save($child);
            $this->_updateChildrenUrl($child);
        }
    }

    private function _addCategoryUrlToIndex(Category $category): void
    {
        $this->_urlIndexService->addUrl(
            new UrlCreateForm([
                'url' => $category->url,
                'module_name' => ShopModule::ID,
                'controller_name' => Category::FRONTEND_CONTROLLER_NAME,
                'action_name' => Category::FRONTEND_ACTION_NAME,
                'entity_id' => (string) $category->id,
            ])
        );
    }

    private function _updateCategoryUrlInIndex(Category $category): void
    {
        $this->_urlIndexService->updateUrlInIndex(new UrlUpdateForm([
            'url' => $category->url,
            'module_name' => ShopModule::ID,
            'controller_name' => Category::FRONTEND_CONTROLLER_NAME,
            'action_name' => Category::FRONTEND_ACTION_NAME,
            'entity_id' => (string) $category->id,
        ]));
    }
}