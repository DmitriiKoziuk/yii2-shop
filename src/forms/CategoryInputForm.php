<?php
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

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'name_on_site'], 'string', 'max' => 45],
            [['slug'], 'string', 'max' => 60],
            [['description'], 'string'],
            [['meta_title'], 'string', 'max' => 255],
            [['meta_description'], 'string', 'max' => 500],
            [['parent_id'], 'integer'],
            [['name', 'name_on_site', 'slug', 'url', 'meta_title', 'meta_description'], 'trim'],
        ];
    }
}