<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\tests;

use Yii;
use yii\di\Container;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2Shop\tests\_fixtures\CategoryFixture;
use DmitriiKoziuk\yii2Shop\tests\_fixtures\CategoryClosureFixture;
use DmitriiKoziuk\yii2Shop\forms\CategoryInputForm;
use DmitriiKoziuk\yii2Shop\services\category\CategoryService;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\exceptions\category\CategoryInputFormNotValid;

class CategoryServiceCreateCategoryTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'category' => [
                'class' => CategoryFixture::class,
                'dataFile' => codecept_data_dir() . 'category_data.php'
            ],
            'category-closure' => [
                'class' => CategoryClosureFixture::class,
                'dataFile' => codecept_data_dir() . 'category_closure_data.php'
            ],
        ];
    }

    protected function _after()
    {
        Yii::$container = new Container();
    }

    public function testCategoryInputFormNotValid()
    {
        /** @var CategoryService $categoryService */
        $categoryService = Yii::$container->get(CategoryService::class);
        $categoryInputForm = new CategoryInputForm();
        $this->expectException(CategoryInputFormNotValid::class);
        $categoryService->createCategory($categoryInputForm);
    }

    public function testCreateRootCategory()
    {
        /** @var CategoryService $categoryService */
        $categoryService = Yii::$container->get(CategoryService::class);
        $this->assertInstanceOf(CategoryService::class, $categoryService);
        $categoryAttributes = [
            'name' => 'Some root category name',
        ];
        $this->tester->dontSeeRecord(Category::class, ['name' => $categoryAttributes['name']]);
        $categoryInputForm = new CategoryInputForm($categoryAttributes);
        $this->assertTrue($categoryInputForm->validate());
        $createdCategory = $categoryService->createCategory($categoryInputForm);
        $this->assertEquals(
            $createdCategory->getAttributes(['name']),
            $categoryAttributes
        );
        $this->assertInstanceOf(Category::class, $createdCategory);
        $this->tester->seeRecord(Category::class, ['name' => $categoryAttributes['name']]);
        $this->tester->seeRecord(UrlEntity::class, ['url' => $createdCategory->url]);
    }

    public function testCreateSubcategory()
    {
        $parentCategoryId = 1;
        $this->tester->seeRecord(Category::class, ['id' => $parentCategoryId]);
        /** @var CategoryService $categoryService */
        $categoryService = Yii::$container->get(CategoryService::class);
        $categoryAttributes = [
            'parent_id' => $parentCategoryId,
            'name' => 'Some sub category name',
        ];
        $this->tester->dontSeeRecord(Category::class, ['name' => $categoryAttributes['name']]);
        $categoryInputForm = new CategoryInputForm($categoryAttributes);
        $this->assertTrue($categoryInputForm->validate());
        $createdCategory = $categoryService->createCategory($categoryInputForm);
        $this->assertEquals(
            $createdCategory->getAttributes(['parent_id', 'name']),
            $categoryAttributes
        );
        $this->assertInstanceOf(Category::class, $createdCategory);
        $this->tester->seeRecord(Category::class, ['name' => $categoryAttributes['name']]);
        /** @var Category $parentCategory */
        $parentCategory = Category::find()
            ->with(['children'])
            ->where(['id' => $parentCategoryId])
            ->one();
        $this->assertIsArray($parentCategory->children);
        $this->assertArrayHasKey($createdCategory->id, $parentCategory->children);
        $this->assertEquals($createdCategory['name'], $parentCategory->children[ $createdCategory->id ]->name);
        $this->tester->seeRecord(UrlEntity::class, ['url' => $createdCategory->url]);
    }

    /**
     * Test creation subcategory chain where next category is child for previous.
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function testCreateSubcategoryChain()
    {
        $subCategoryChain = [
            'Second level category',
            'Third level category',
            'Fourth level category',
            'Fifth level category',
            'Sixth level category',
            'Seventh level category',
            'Eighth level category',
            'Ninth level category',
            'Tenth level category',
        ];
        $createdCategories = [];
        $rootCategory = 1;
        $parentCategoryId = $rootCategory;
        $this->tester->seeRecord(Category::class, ['id' => $parentCategoryId]);
        /** @var CategoryService $categoryService */
        $categoryService = Yii::$container->get(CategoryService::class);
        /**
         * creat subcategory chain where next category is child for previous
         */
        foreach ($subCategoryChain as $subCategoryName) {
            $categoryAttributes = [
                'parent_id' => $parentCategoryId,
                'name' => $subCategoryName,
            ];
            $this->tester->dontSeeRecord(Category::class, ['name' => $categoryAttributes['name']]);
            $categoryInputForm = new CategoryInputForm($categoryAttributes);
            $this->assertTrue($categoryInputForm->validate());
            $createdCategory = $categoryService->createCategory($categoryInputForm);
            $this->assertEquals(
                $createdCategory->getAttributes(['parent_id', 'name']),
                $categoryAttributes
            );
            $this->assertInstanceOf(Category::class, $createdCategory);
            $this->tester->seeRecord(Category::class, [
                'id' => $createdCategory->id,
                'name' => $categoryAttributes['name'],
            ]);
            $parentCategoryId = $createdCategory->id;
            $createdCategories[ $createdCategory->id ] = $createdCategory;
        }
        /**
         * is first parent category has all child
         */
        /** @var Category $parentCategory */
        $parentCategory = Category::find()
            ->with(['children'])
            ->where(['id' => $rootCategory])
            ->one();
        $this->assertIsArray($parentCategory->children);
        foreach ($createdCategories as $createdCategory) {
            $this->assertArrayHasKey($createdCategory->id, $parentCategory->children);
            $this->assertEquals($createdCategory['name'], $parentCategory->children[ $createdCategory->id ]->name);
        }
    }
}