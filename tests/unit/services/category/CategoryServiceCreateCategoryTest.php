<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\tests;

use Yii;
use yii\di\Container;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2Shop\tests\_fixtures\CategoryFixture;
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
        $this->assertInstanceOf(Category::class, $createdCategory);
        $this->tester->seeRecord(Category::class, ['name' => $categoryAttributes['name']]);
        $this->tester->seeRecord(UrlEntity::class, ['url' => $createdCategory->url]);
    }
}