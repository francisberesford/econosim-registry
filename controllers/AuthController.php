<?php
namespace app\controllers;
 
use app\models\LoginForm;
use app\components\AuthorizeFilter;
use conquer\oauth2\TokenAction;
 
class AuthController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    
    public function behaviors()
    {
        return [
            /** 
             * checks oauth2 credentions
             * and performs OAuth2 authorization, if user is logged on
             */
            'oauth2Auth' => [
                'class' => AuthorizeFilter::className(),
                'only' => ['index'],
            ],
        ];
    }
    
    public function actions()
    {
        return [
            // returns access token
            'token' => [
                'class' => TokenAction::classname(),
            ],
        ];
    }
    /**
     * Display login form to authorize user
     */
    public function actionIndex()
    {
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }
}