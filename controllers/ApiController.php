<?php
namespace app\controllers;

use Yii;
use conquer\oauth2\TokenAuth;
use yii\web\Response;
use app\models\Game;

class ApiController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            // performs authorization by token
            'tokenAuth' => [
                'class' => TokenAuth::className(),
            ],
        ];
    }
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }
    /**
     * Returns username and email
     */
    public function actionIndex()
    {
        $user = \Yii::$app->user->identity;
        return [
            'username' => $user->username,
        ];
    }

    public function actionUpdateGame()
    {
        $data = Yii::$app->request->post();
        $ip = ip2long(Yii::$app->request->userIP);
        foreach($data['Games'] as $game)
        {
            $Game = Game::find()->where([
                'id' => $game['id'],
                'ip_address' => $ip,
            ])->one();
            
            if(!$Game) {
                $Game = new Game;
            }
            
            $Game->attributes = $game;
            $Game->ip_address = $ip;
            $Game->save();
        }
        
        return true;
    }
}