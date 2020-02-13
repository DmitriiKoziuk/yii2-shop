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
        $stageLogTableName = OrderStageLog::tableName();

        $orderStageLogSubQuery = OrderStageLog::find()
            ->select([
                "MAX({$stageLogTableName}.id) AS id",
                "{$stageLogTableName}.order_id AS order_id"
            ])
            ->groupBy([OrderStageLog::tableName() . '.order_id']);

        $query = Order::find()
            ->innerJoin(
                ['osl' => $orderStageLogSubQuery],
                ['osl.order_id' => new Expression(Order::tableName() . '.id')])
            ->innerJoin(
                OrderStageLog::tableName(),
                [
                    OrderStageLog::tableName() . '.id' => new Expression('osl.id'),
                ]
            );

        $query->with([
            'cart.cartProductSkus.productSku.product.type',
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
            Order::tableName() . '.id' => $this->id,
            OrderStageLog::tableName() . '.user_id' => $this->user_id,
        ]);

        $new = OrderStageLog::STATUS_NEW;
        $inWork = OrderStageLog::STATUS_IN_WORK;
        $suspended = OrderStageLog::STATUS_SUSPEND;
        $done = OrderStageLog::STATUS_DONE;
        $deleted = OrderStageLog::STATUS_DELETED;

        $query->orderBy([
            new Expression("FIELD({$stageLogTableName}.stage_id, {$new}, {$inWork}, {$suspended}, {$done}, {$deleted})"),
            OrderStageLog::tableName() . '.created_at' => SORT_ASC,
        ]);

        return $dataProvider;
    }
}
