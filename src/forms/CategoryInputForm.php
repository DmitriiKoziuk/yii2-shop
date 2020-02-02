<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms;

use yii\base\Model;

class CategoryInputForm extends Model
{
    public $parent_id;
    public $name;
    public $name_on_site;
    public $slug;
    public $meta_title;
    public $meta_description;
    public $description;
    public $is_products_show;
    public $template_name;
    public $filtered_title_template;
    public $filtered_description_template;
    public $filtered_h1_template;
    public $load_faceted_navigation;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'name_on_site'], 'string', 'max' => 45],
            [['slug'], 'string', 'max' => 60],
            [['description'], 'string'],
            [
                [
                    'meta_title',
                    'filtered_title_template',
                    'filtered_description_template',
                    'filtered_h1_template',
                ],
                'string', 'max' => 255
            ],
            [['meta_description'], 'string', 'max' => 500],
            [['template_name'], 'string', 'max' => 100],
            [['parent_id', 'is_products_show', 'load_faceted_navigation'], 'integer'],
            [
                [
                    'name',
                    'name_on_site',
                    'slug',
                    'meta_title',
                    'meta_description',
                    'template_name',
                    'filtered_title_template',
                    'filtered_description_template',
                    'filtered_h1_template',
                ],
                'trim'
            ],
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
            'filtered_title_template',
            'filtered_description_template',
            'filtered_h1_template',
            'load_faceted_navigation',
        ]);
    }
}
