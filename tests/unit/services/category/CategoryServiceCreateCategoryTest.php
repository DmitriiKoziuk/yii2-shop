<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\tests;

use Yii;
use yii\di\Container;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2Shop\forms\CategoryInputForm;
use DmitriiKoziuk\yii2Shop\services\category\CategoryService;
use DmitriiKoziuk\yii2Shop\entities\Category;

class CategoryServiceCreateCategoryTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;


    protected function _after()
    {
        Yii::$container = new Container();
    }


    public function testCreateRootCategory()
    {
        /** @var CategoryService $categoryService */
        $categoryService = Yii::$container->get(CategoryService::class);
        $this->assertInstanceOf(CategoryService::class, $categoryService);
        $this->tester->dontSeeRecord(Category::class, ['name' => 'Electronics']);
        $categoryAttributes = [
            'name' => 'Some root category name',
        ];
        $categoryInputForm = new CategoryInputForm($categoryAttributes);
        $this->assertTrue($categoryInputForm->validate());
        $createdCategory = $categoryService->createCategory($categoryInputForm);
        $this->assertInstanceOf(Category::class, $createdCategory);
        $this->tester->seeRecord(Category::class, $categoryAttributes);
        $this->tester->seeRecord(UrlEntity::class, ['url' => $createdCategory->url]);
    }
}