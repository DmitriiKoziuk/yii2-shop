<?php
namespace DmitriiKoziuk\yii2Shop\entities\search;

use yii\base\Model;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

class ProductSkuSearch extends ProductSku
{
    public $product_name;
    public $sku_name;
    public $type_id;
    public $category_id;
    public $brand_id;

    public function rules()
    {
        return [
            [['id', 'type_id', 'category_id', 'stock_status', 'currency_id', 'brand_id'], 'integer'],
            [['product_name', 'sku_name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function by($params)
    {
        $query = ProductSku::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if (! empty($this->sku_name)) {
            $nameParts = explode(' ', $this->sku_name);
            foreach ($nameParts as $part) {
                $query->andWhere(
                    ['like', ProductSku::tableName(). '.name', $part]
                );
            }
        }

        if (! empty($this->type_id)     ||
            ! empty($this->category_id) ||
            ! empty($this->product_name) ||
            ! empty($this->brand_id)
        ) {
            $on[ Product::tableName() . '.id' ] = new Expression(ProductSku::tableName() . '.product_id');
            $params = [];
            if (! empty($this->type_id)) {
                $on[ Product::tableName() . '.type_id' ] = new Expression(':typeID');
                $params[':typeID'] = $this->type_id;
            }
            if (! empty($this->category_id)) {
                $on[ Product::tableName() . '.category_id' ] = new Expression(':categoryID');
                $params[':categoryID'] = $this->category_id;
            }
            if (! empty($this->product_name)) {
                $nameParts = explode(' ', $this->product_name);
                foreach ($nameParts as $part) {
                    $query->andWhere(
                        ['like', Product::tableName(). '.name', $part]
                    );
                }
            }
            if (! empty($this->brand_id)) {
                $on[ Product::tableName() . '.brand_id' ] = new Expression(':brandID');
                $params[':brandID'] = $this->brand_id;
            }
            $query->innerJoin(
                Product::tableName(),
                $on,
                $params
            );
        }

        return $dataProvider;
    }
}