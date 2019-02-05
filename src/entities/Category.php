<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
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
            [
                [
                    'name_on_site',
                    'description',
                    'meta_title',
                    'meta_description',
                ],
                'default', 'value' => null
            ],
            [['parent_id', 'created_at', 'updated_at'], 'integer'],
            [['parent_id'], 'default', 'value' => null],
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
            'created_at'       => Yii::t('app', 'Created at'),
            'updated_at'       => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function _getParents()
    {
        return $this->hasMany(Category::class, ['id' => 'ancestor'])
            ->viaTable(CategoryClosure::tableName(), ['descendant' => 'id'])
            ->select('{{%category}}.*, {{%category_closure}}.depth as depth')
            ->leftJoin(CategoryClosure::tableName(), '{{%category_closure}}.ancestor = {{%category}}.id AND {{%category_closure}}.descendant = ' . $this->id)
            ->orderBy('depth')
            ->indexBy('depth');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentList()
    {
        return $this->hasMany(Category::class, ['id' => 'ancestor'])
            ->viaTable(CategoryClosure::tableName(), ['descendant' => 'id'])
            ->indexBy('id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryClosure()
    {
        return $this->hasMany(CategoryClosure::class, ['descendant' => 'id'])
            ->indexBy('depth');
    }

    public function getParents()
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
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::class, ['id' => 'descendant'])
            ->viaTable(CategoryClosure::tableName(), ['ancestor' => 'id'], function ($q) {
                /** @var $q ActiveQuery */
                $q->orderBy('depth');
            });
    }

    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectDescendants()
    {
        return $this->hasMany(static::class, ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable(CategoryProduct::tableName(), ['category_id' => 'id']);
    }

    public function getParentsNames()
    {
        $r = '';
        foreach ($this->parents as $parent) {
            $r .= $parent->name . ' > ';
        }
        return $r;
    }
}
