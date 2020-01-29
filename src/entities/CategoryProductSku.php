<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2Shop\repositories\CategoryProductSkuRepository;

/**
 * This is the model class for table "{{%dk_shop_category_product_sku}}".
 *
 * @property int $category_id
 * @property int $product_sku_id
 * @property int $sort           Use getSort() instead direct access
 *
 * @property Category   $category
 * @property ProductSku $productSku
 */
class CategoryProductSku extends ActiveRecord
{
    /**
     * @var CategoryProductSkuRepository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_category_product_sku}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'product_sku_id', 'sort'], 'required'],
            [['category_id', 'product_sku_id', 'sort'], 'integer'],
            [['category_id', 'sort'], 'unique', 'targetAttribute' => ['category_id', 'sort']],
            [['category_id', 'product_sku_id'], 'unique', 'targetAttribute' => ['category_id', 'product_sku_id']],
            [
                ['category_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Category::class,
                'targetAttribute' => ['category_id' => 'id']
            ],
            [
                ['product_sku_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProductSku::class,
                'targetAttribute' => ['product_sku_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'category_id'    => Yii::t('app', 'Category ID'),
            'product_sku_id' => Yii::t('app', 'Product Sku ID'),
            'sort'           => Yii::t('app', 'Sort'),
        ];
    }

    public function init()
    {
        $this->repository = Yii::$container->get(CategoryProductSkuRepository::class);
    }

    public function afterFind()
    {
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getProductSku(): ActiveQuery
    {
        return $this->hasOne(ProductSku::class, ['id' => 'product_sku_id']);
    }

    public function getSort()
    {
        $maxSort = $this->repository->getMaxSort($this->category_id);
        $sort = $maxSort - $this->sort;
        if ($sort == 0) {
            return 1;
        }
        return $sort;
    }

    public function getMaxSort(): int
    {
        return $this->repository->getMaxSort($this->category_id);
    }

    public function getMinSort(): int
    {
        return 1;
    }
}
