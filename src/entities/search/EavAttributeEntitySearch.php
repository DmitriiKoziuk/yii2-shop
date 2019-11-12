<?php

namespace DmitriiKoziuk\yii2Shop\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

/**
 * EavAttributeEntitySearch represents the model behind the search form of `DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity`.
 */
class EavAttributeEntitySearch extends EavAttributeEntity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'selectable', 'multiple', 'value_type_id', 'default_value_type_unit_id'], 'integer'],
            [['name', 'code', 'storage_type'], 'safe'],
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
        $query = EavAttributeEntity::find();

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
            'selectable' => $this->selectable,
            'multiple' => $this->multiple,
            'value_type_id' => $this->value_type_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'storage_type', $this->storage_type]);

        return $dataProvider;
    }
}
