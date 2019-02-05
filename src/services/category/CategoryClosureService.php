<?php
namespace DmitriiKoziuk\yii2Shop\services\category;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\entities\CategoryClosure;
use DmitriiKoziuk\yii2Shop\repositories\CategoryClosureRepository;
use DmitriiKoziuk\yii2Shop\repositories\CategoryRepository;

final class CategoryClosureService extends EntityActionService
{
    /**
     * @var CategoryClosureRepository
     */
    private $_categoryClosureRepository;

    /**
     * @var CategoryRepository
     */
    private $_categoryRepository;

    public function __construct(
        CategoryClosureRepository $categoryClosureRepository,
        CategoryRepository $categoryRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_categoryClosureRepository = $categoryClosureRepository;
        $this->_categoryRepository = $categoryRepository;
    }

    /**
     * @param Category $category
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \yii\db\Exception
     */
    public function updateRelations(Category $category): void
    {
        $this->_deleteClosure($category);
        $this->_createClosure($category);
        foreach ($category->directChildren as $child) {
            $this->updateRelations($child);
        }
    }

    /**
     * @param Category $category
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     */
    private function _createClosure(Category $category): void
    {
        if (! empty($category->parent_id)) {
            /** @var Category $parentCategory */
            $parentCategory = $this->_categoryRepository->getParentCategory($category);

            $depth = 0;

            foreach ($parentCategory->parents as $parent) {
                $new = new CategoryClosure([
                    'ancestor'   => $parent->id,
                    'descendant' => $category->id,
                    'depth'      => $depth
                ]);

                $this->_categoryClosureRepository->save($new);

                $depth++;
            }

            $new = new CategoryClosure([
                'ancestor'   => $parentCategory->id,
                'descendant' => $category->id,
                'depth'      => $depth
            ]);

            $this->_categoryClosureRepository->save($new);
        }
    }

    /**
     * @param Category $category
     * @throws \yii\db\Exception
     */
    private function _deleteClosure(Category $category): void
    {
        \Yii::$app->db->createCommand()
            ->delete(CategoryClosure::tableName(), ['descendant' => $category->id])
            ->execute();
    }
}