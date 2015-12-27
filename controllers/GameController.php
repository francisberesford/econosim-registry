<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Game;

class GameController extends Controller
{
    public $layout = 'main';
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['play', 'design'],
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                // everything else is denied
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        //@todo: move to cron job or something
        Game::removeInactive();
        
        $Games = Game::find()->all();
        return $this->render('index', [
            'Games' => $Games,
        ]);
    }
    
    public function actionPlay($id)
    {
        $Game = Game::findOne($id);
        $this->layout = 'game';
        return $this->render('play', [
            'Game' => $Game,
            'User' => Yii::$app->user->identity,
        ]);
    }
    
    public function actionDesignCity()
    {
        $set = RBEGame::getSet('venus');
        $this->layout = 'design';
        return $this->render('design_city', [
            'User' => Yii::$app->user->identity,
            'set' => $set,
        ]);
    }
    
    public function actionDesignOld()
    {
        $set = RBEGame::getSet('venus');
        $this->layout = 'design';
        return $this->render('design_old', [
            'User' => Yii::$app->user->identity,
            'set' => $set,
        ]);
    }
    
    //@todo: make this a proper REST web service
    public function actionRegister($gameName, $port, $host=null)
    {
        $ip = ip2long(Yii::$app->request->userIP);
        $Game = Game::find()->where([
            'ip_address' => $ip,
            'port' => $port,
        ])->one();
        
        if(!$Game)
        {
            $Game = new Game;
            $Game->name = $gameName;
            $Game->ip_address = $ip;
            $Game->port = $port;
            $Game->host = $host;
        }
        return $Game->save();
    }
}
