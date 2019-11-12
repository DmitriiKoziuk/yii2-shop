<?php

namespace DmitriiKoziuk\yii2Shop\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;

/**
 * EavValueDoubleEntitySearch represents the model behind the search form of `DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity`.
 */
class EavValueDoubleEntitySearch extends EavValueDoubleEntity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'attribute_id', 'value_type_unit_id'], 'integer'],
            [['value'], 'number'],
            [['code'], 'string'],
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
        $query = EavValueDoubleEntity::find();

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
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'value' => $this->value,
            'value_type_unit_id' => $this->value_type_unit_id,
        ]);

        return $dataProvider;
    }
}
