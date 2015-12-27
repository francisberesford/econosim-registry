<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\models\Game;

/**
 * GameSearch represents the model behind the search form about `app\models\Game`.
 */
class GameSearch extends Game
{
    public $host_search;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ip_address'], 'integer'],
            [['name', 'host_search'], 'safe'],
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
        $query = Game::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->where(['>', 'updated_at', time() - Yii::$app->params['game_list_timeout']]);

        $query->andFilterWhere([
            'id' => $this->id,
            'ip_address' => $this->ip_address,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'launch_url', $this->host_search]);

        return $dataProvider;
    }
}
