<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\base\InvalidConfigException;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_categories}}".
 *
 * @property int    $id
 * @property string $name
 * @property string $name_on_site
 * @property string $slug
 * @property string $url
 * @property int    $parent_id
 * @property string $meta_title
 * @property string $meta_description
 * @property string $description
 * @property int    $is_products_show
 * @property string $template_name
 * @property int    $created_at
 * @property int    $updated_at
 *
 * @property Category          $parent
 * @property Category[]        $parents
 * @property Category[]        $children
 * @property Category[]        $parentList
 * @property Category[]        $directChildren
 * @property Category[]        $directDescendants
 * @property CategoryClosure[] $categoryClosure
 * @property Product[]         $products
 */
class Category extends ActiveRecord
{
    const FRONTEND_CONTROLLER_NAME = 'category';
    const FRONTEND_ACTION_NAME     = 'index';

    const IS_PRODUCT_SHOW_FALSE = 0;
    const IS_PRODUCT_SHOW_TRUE = 1;

    public $depth;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dk_shop_categories}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'url'], 'required'],
            [['name', 'name_on_site'], 'string', 'max' => 45],
            [['slug'], 'string', 'max' => 60],
            [['url'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['meta_title'], 'string', 'max' => 255],
            [['meta_description'], 'string', 'max' => 500],
            [['template_name'], 'string', 'max' => 100],
            [
                [
                    'name_on_site',
                    'description',
                    'meta_title',
                    'meta_description',
                    'template_name',
                ],
                'default', 'value' => null
            ],
            [['parent_id', 'is_products_show', 'created_at', 'updated_at'], 'integer'],
            [['parent_id'], 'default', 'value' => null],
            [['is_products_show'], 'default', 'value' => 1],
            [
                ['parent_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Category::class,
                'targetAttribute' => ['parent_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'ID'),
            'name'             => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Name'),
            'name_on_site'     => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Name on site'),
            'slug'             => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Slug'),
            'url'              => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Url'),
            'parent_id'        => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Parent ID'),
            'meta_title'       => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Meta Title'),
            'meta_description' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Meta Description'),
            'description'      => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Description'),
            'is_products_show' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Is products show'),
            'template_name'    => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Template name'),
            'created_at'       => Yii::t('app', 'Created at'),
            'updated_at'       => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery|self[]
     * @throws InvalidConfigException
     */
    public function getParentList()
    {
        return $this->hasMany(Category::class, ['id' => 'ancestor'])
            ->viaTable(CategoryClosure::tableName(), ['descendant' => 'id'])
            ->indexBy('id');
    }

    /**
     * @return ActiveQuery|CategoryClosure[]
     */
    public function getCategoryClosure()
    {
        return $this->hasMany(CategoryClosure::class, ['descendant' => 'id'])
            ->indexBy('depth');
    }

    /**
     * @return self[]
     */
    public function getParents(): array
    {
        $parents = [];
        if (! empty($this->parent_id)) {
            foreach ($this->categoryClosure as $closure) {
                $parents[ $closure->depth ] = $this->parentList[ $closure->ancestor ];
            }
        }

        return $parents;
    }

    /**
     * @return ActiveQuery|self[]
     * @throws InvalidConfigException
     */
    public function getChildren()
    {
        return $this->hasMany(Category::class, ['id' => 'descendant'])
            ->viaTable(CategoryClosure::tableName(), ['ancestor' => 'id'], function ($q) {
                /** @var $q ActiveQuery */
                $q->orderBy('depth');
            })
            ->indexBy('id');
    }

    /**
     * @param self[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @return ActiveQuery|self[]
     */
    public function getDirectChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    /**
     * @return ActiveQuery|self[]
     */
    public function getDirectDescendants()
    {
        return $this->hasMany(static::class, ['parent_id' => 'id']);
    }

    /**
     * @return ActiveQuery|Product[]
     * @throws InvalidConfigException
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable(CategoryProduct::tableName(), ['category_id' => 'id']);
    }

    public function getParentsNames(): string
    {
        $r = '';
        foreach ($this->parents as $parent) {
            $r .= $parent->name . ' > ';
        }
        return $r;
    }

    public function getFrontendName(): string
    {
        return $this->name_on_site ?? $this->name;
    }
}
