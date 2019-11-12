<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\tests;

use Yii;
use yii\di\Container;
use Faker\Provider\Base;
use DmitriiKoziuk\yii2Shop\forms\CategoryInputForm;

class CategoryInputFormTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _after()
    {
        Yii::$container = new Container();
    }

    /**
     * @param array $attributes
     * @dataProvider validDataProvider
     */
    public function testValid(array $attributes)
    {
        $form = new CategoryInputForm($attributes);
        $this->assertTrue($form->validate());
    }

    /**
     * @param string $attributeName
     * @param $attributeValue
     * @param string $attributeErrorMessage
     * @dataProvider notValidDataProvider
     */
    public function testNotValidAttributes(string $attributeName, $attributeValue, string $attributeErrorMessage)
    {
        $form = new CategoryInputForm([$attributeName => $attributeValue]);
        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors($attributeName));
        $this->assertContains($attributeErrorMessage, $form->getErrors()[ $attributeName ]);
    }

    public function validDataProvider()
    {
        return [
            'minimum valid data' => [
                [
                    'name' => 'Some category name',
                ],
            ],
        ];
    }

    public function notValidDataProvider()
    {
        return [
            'name blank' => [
                'name',
                '',
                'Name cannot be blank.'
            ],
            'name max length 45' => [
                'name',
                Base::lexify(str_repeat('?', 46)),
                'Name should contain at most 45 characters.',
            ],
            'name on site max length 45' => [
                'name_on_site',
                Base::lexify(str_repeat('?', 46)),
                'Name On Site should contain at most 45 characters.',
            ],
            'slug max length 60' => [
                'slug',
                Base::lexify(str_repeat('?', 61)),
                'Slug should contain at most 60 characters.',
            ],
            'url not start from "/"' => [
                'url',
                'not-valid-url.html',
                'Url must start from "/" character.',
            ],
            'meta_title max length 255' => [
                'meta_title',
                Base::lexify(str_repeat('?', 266)),
                'Meta Title should contain at most 255 characters.',
            ],
            'meta_description max length 500' => [
                'meta_description',
                Base::lexify(str_repeat('?', 501)),
                'Meta Description should contain at most 500 characters.',
            ],
        ];
    }
}