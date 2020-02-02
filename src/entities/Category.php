<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

/**
 * This is the model class for table "{{%dk_shop_categories}}".
 *
 * @property int    $id
 * @property string $name
 * @property string $name_on_site
 * @property string $slug
 * @property int    $parent_id
 * @property string $meta_title
 * @property string $meta_description
 * @property string $description
 * @property int    $is_products_show
 * @property string $template_name
 * @property int    $created_at
 * @property int    $updated_at
 * @property string $filtered_title_template
 * @property string $filtered_description_template
 * @property string $filtered_h1_template
 * @property int    $load_faceted_navigation
 *
 * @property Category          $parent
 * @property Category[]        $parents
 * @property Category[]        $children
 * @property Category[]        $parentList
 * @property Category[]        $directChildren
 * @property Category[]        $directDescendants
 * @property CategoryClosure[] $categoryClosure
 * @property Product[]         $products
 * @property UrlEntity         $urlEntity
 */
class Category extends ActiveRecord
{
    const FRONTEND_CONTROLLER_NAME = 'category';
    const FRONTEND_ACTION_NAME     = 'index';

    const IS_PRODUCT_SHOW_FALSE = 0;
    const IS_PRODUCT_SHOW_TRUE = 1;

    const LOAD_FACETED_NAVIGATION_NO = 0;
    const LOAD_FACETED_NAVIGATION_YES = 1;

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
            [['name', 'slug'], 'required'],
            [['name', 'name_on_site'], 'string', 'max' => 45],
            [['slug'], 'string', 'max' => 60],
            [['description'], 'string'],
            [
                [
                    'meta_title',
                    'filtered_title_template',
                    'filtered_description_template',
                    'filtered_h1_template',
                ], 'string', 'max' => 255],
            [['meta_description'], 'string', 'max' => 500],
            [['template_name'], 'string', 'max' => 100],
            [
                [
                    'name_on_site',
                    'description',
                    'meta_title',
                    'meta_description',
                    'template_name',
                    'filtered_title_template',
                    'filtered_description_template',
                    'filtered_h1_template',
                ],
                'default', 'value' => null
            ],
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
            [
                [
                    'parent_id',
                    'is_products_show',
                    'created_at',
                    'updated_at',
                    'load_faceted_navigation',
                ],
                'integer'
            ],
            [['parent_id'], 'default', 'value' => null],
            [['is_products_show', 'load_faceted_navigation'], 'default', 'value' => 1],
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
            'parent_id'        => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Parent ID'),
            'meta_title'       => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Meta Title'),
            'meta_description' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Meta Description'),
            'description'      => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Description'),
            'is_products_show' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Is products show'),
            'template_name'    => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Template name'),
            'created_at'       => Yii::t('app', 'Created at'),
            'updated_at'       => Yii::t('app', 'Updated at'),
            'filtered_title_template' => 'Filtered title template',
            'filtered_description_template' => 'Filtered description template',
            'filtered_h1_template' => 'Filtered h1 template',
            'load_faceted_navigation' => 'Load faceted navigation',
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }

    public function getParent(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getParentList(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'ancestor'])
            ->viaTable(CategoryClosure::tableName(), ['descendant' => 'id'])
            ->indexBy('id');
    }

    public function getCategoryClosure(): ActiveQuery
    {
        return $this->hasMany(CategoryClosure::class, ['descendant' => 'id'])
            ->indexBy('depth');
    }

    /**
     * @throws InvalidConfigException
     */
    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'descendant'])
            ->viaTable(CategoryClosure::tableName(), ['ancestor' => 'id'], function ($q) {
                /** @var $q ActiveQuery */
                $q->orderBy('depth');
            })
            ->indexBy('id');
    }

    public function getDirectChildren(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id'])
            ->orderBy(['name' => SORT_ASC]);
    }

    public function getDirectDescendants(): ActiveQuery
    {
        return $this->hasMany(static::class, ['parent_id' => 'id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable(CategoryProduct::tableName(), ['category_id' => 'id']);
    }

    public function getUrlEntity(): ActiveQuery
    {
        return $this->hasOne(UrlEntity::class, ['entity_id' => 'id'])
            ->andWhere([
                'module_name' => ShopModule::getId(),
                'controller_name' => self::FRONTEND_CONTROLLER_NAME,
                'action_name' => self::FRONTEND_ACTION_NAME,
            ]);
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

    public function isFilteredTitleTemplateSet(): bool
    {
        return ! empty($this->filtered_title_template);
    }

    public function isFilteredDescriptionTemplateSet(): bool
    {
        return ! empty($this->filtered_description_template);
    }

    public function isFilteredH1TemplateSet(): bool
    {
        return ! empty($this->filtered_h1_template);
    }

    public function isLoadFacetedNavigation(): bool
    {
        return (bool) $this->load_faceted_navigation;
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

    public function getFilteredTitle(array $filteredAttributes, Brand $filteredBrand = null): string
    {
        if ($this->isFilteredTitleTemplateSet()) {
            return $this->templateParser(
                $this->filtered_title_template,
                $filteredAttributes,
                $filteredBrand
            );
        }
        return $this->name;
    }

    public function getFilteredDescription(array $filteredAttributes, Brand $filteredBrand = null): string
    {
        if ($this->isFilteredDescriptionTemplateSet()) {
            return $this->templateParser(
                $this->filtered_description_template,
                $filteredAttributes,
                $filteredBrand
            );
        }
        return $this->meta_description;
    }

    public function getFilteredH1(array $filteredAttributes, Brand $filteredBrand = null): string
    {
        if ($this->isFilteredH1TemplateSet()) {
            return $this->templateParser(
                $this->filtered_h1_template,
                $filteredAttributes,
                $filteredBrand
            );
        }
        return $this->name;
    }

    /**
     * @param self[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @param string $template
     * @param EavAttributeEntity[] $filteredAttributes
     * @param Brand $filteredBrand
     * @return string
     */
    private function templateParser(
        string $template,
        array $filteredAttributes,
        Brand $filteredBrand = null
    ): string
    {
        $r = '';
        if (false !== preg_match_all('/{+(.*?)}/', $template, $matches)) {
            if (empty($matches[0])) {
                return '';
            }
            $elements = array_map(function ($fullMatch, $subPattern) {
                return [
                    'fullMatch' => $fullMatch,
                    'subPattern' => $subPattern,
                ];
            }, $matches[0], $matches[1]);
            $elements = ArrayHelper::map($elements, 'fullMatch', 'subPattern');
            foreach ($elements as $match => $sub) {
                $elements[ $match ] = $this->parseElement($sub, $filteredAttributes, $filteredBrand);
            }
            $r = trim(preg_replace('/\s{2,}/', ' ', strtr($template, $elements)));
        }
        return $r;
    }

    /**
     * @param string $element
     * @param EavAttributeEntity[] $facetedAttributes
     * @return string
     */
    private function parseElement(string $element, array $facetedAttributes, Brand $filteredBrand = null): string
    {
        $returnString = '';
        list($attributeCode, $valueSeoCode) = explode(':', $element);
        if ('brand' == $attributeCode && ! empty($filteredBrand)) {
            $returnString = $filteredBrand->name;
        } else {
            foreach ($facetedAttributes as $attribute) {
                if ($attributeCode == $attribute->code && ! empty($attribute->values)) {
                    foreach ($attribute->values as $value) {
                        if ($attribute->storage_type == EavAttributeEntity::STORAGE_TYPE_DOUBLE) {
                            $returnString = ', ' . $value->value;
                        } elseif ($attribute->storage_type == EavAttributeEntity::STORAGE_TYPE_VARCHAR) {
                            if (isset($value->seoValues[$valueSeoCode])) {
                                $returnString = ', ' . $value->seoValues[ $valueSeoCode ]->value;
                            } else {
                                $returnString = ', ' . $value->value;
                            }
                        }
                    }
                }
            }
        }
        $returnString = ltrim($returnString, ', ');
        return $returnString;
    }
}
