<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities\search;

use yii\db\Expression;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\entities\Order;
use DmitriiKoziuk\yii2Shop\entities\OrderStageLog;

/**
 * OrderSearch represents the model behind the search form of `DmitriiKoziuk\yii2Shop\entities\Order`.
 */
class OrderSearch extends Order
{
    public $user_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['customer_comment', 'user_id'], 'safe'],
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
        $new = OrderStageLog::STATUS_NEW;
        $inWork = OrderStageLog::STATUS_IN_WORK;
        $suspended = OrderStageLog::STATUS_SUSPEND;
        $done = OrderStageLog::STATUS_DONE;
        $deleted = OrderStageLog::STATUS_DELETED;

        $ordersTableName = Order::tableName();
        $stageLogTableName = OrderStageLog::tableName();

        $subQueryOrderStage = OrderStageLog::find()
            ->select([
                OrderStageLog::tableName() . '.stage_id'
            ])
            ->where(["{$stageLogTableName}.order_id" => new Expression("{$ordersTableName}.id")])->limit(1);
        $query = Order::find()
            ->select([
                "{$ordersTableName}.*",
                'current_stage' => $subQueryOrderStage,
            ])
            ->from([Order::tableName()]);
        $query->orderBy([
            "{$ordersTableName}.id" => SORT_ASC,
            new Expression("FIELD(current_stage, {$new}, {$inWork}, {$suspended}, {$done}, {$deleted})"),
        ]);

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

        $query->andFilterWhere(['like', 'customer_comment', $this->customer_comment]);

        return $dataProvider;
    }
}
