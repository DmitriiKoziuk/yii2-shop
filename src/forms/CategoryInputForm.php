<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms;

use yii\base\Model;

class CategoryInputForm extends Model
{
    public $parent_id;
    public $name;
    public $name_on_site;
    public $slug;
    public $url;
    public $meta_title;
    public $meta_description;
    public $description;
    public $is_products_show;
    public $template_name;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'name_on_site'], 'string', 'max' => 45],
            [['slug'], 'string', 'max' => 60],
            [['url'], function ($attribute) {
                if (
                    ! is_null($this->$attribute) &&
                    '/' !== ($firstChar = mb_substr($this->$attribute, 0, 1))
                ) {
                    $this->addError($attribute, 'Url must start from "/" character.');
                }
            }],
            [['description'], 'string'],
            [['meta_title'], 'string', 'max' => 255],
            [['meta_description'], 'string', 'max' => 500],
            [['template_name'], 'string', 'max' => 100],
            [['parent_id', 'is_products_show'], 'integer'],
            [['name', 'name_on_site', 'slug', 'url', 'meta_title', 'meta_description', 'template_name'], 'trim'],
        ];
    }

    public function getChangedAttributes()
    {
        return $this->getAttributes([
            'parent_id',
            'name',
            'name_on_site',
            'slug',
            'meta_title',
            'meta_description',
            'description',
            'is_products_show',
            'template_name',
        ]);
    }
}
