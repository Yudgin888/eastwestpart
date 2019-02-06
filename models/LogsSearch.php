<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class LogsSearch extends Logs
{

    public function rules()
    {
        return [
            [['id', 'date', 'message'], 'trim'],
            ['status', 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Logs::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

//        $query->andFilterWhere([
//            'id' => $this->id,
//            'date' => $this->date,
//            'message' => $this->message,
//            'status' => $this->status,
//        ]);
//
//        $query->andFilterWhere(['like', 'id', $this->id])
//            ->andFilterWhere(['like', 'date', $this->date])
//            ->andFilterWhere(['like', 'message', $this->message])
//            ->andFilterWhere(['like', 'status', $this->status]);
        return $dataProvider;
    }
}