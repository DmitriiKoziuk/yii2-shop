<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity;

/**
 * ProductTypeAttributeEntitySearch represents the model behind the search form of `DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity`.
 */
class ProductTypeAttributeEntitySearch extends ProductTypeAttributeEntity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'product_type_id',
                    'attribute_id',
                    'view_attribute_at_product_sku_preview',
                    'sort_at_product_sku_preview',
                    'sort_at_product_sku_page',
                ],
                'integer'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProductTypeAttributeEntity::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'product_type_id' => $this->product_type_id,
            'attribute_id' => $this->attribute_id,
            'view_attribute_at_product_sku_preview' => $this->view_attribute_at_product_sku_preview,
            'sort_at_product_sku_preview' => $this->sort_at_product_sku_preview,
        ]);

        return $dataProvider;
    }
}
