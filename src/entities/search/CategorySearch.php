<?php
namespace DmitriiKoziuk\yii2Shop\entities\search;

use yii\base\Model;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\entities\Category;

/**
 * CategorySearch represents the model behind the search form about `common\models\Category`.
 */
class CategorySearch extends Category
{
    public $url;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'url'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Category::find()->with(['parentList', 'categoryClosure']);

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        if (! empty($this->url)) {
            $query->joinWith(['page' => function ($q) {
                /** @var $q ActiveQuery */
                $q->where(['controller' => Category::FRONTEND_CONTROLLER_NAME]);
                $q->andWhere(['like', 'url', $this->url]);
            }]);
        }

        return $dataProvider;
    }
}
