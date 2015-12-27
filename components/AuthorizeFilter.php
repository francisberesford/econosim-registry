<?php
namespace app\components;
use yii\web\Response;
use yii\helpers\Json;

use Yii;
/**
 * 
 * @author Francis Beresford
 * 
 */
class AuthorizeFilter extends \conquer\oauth2\AuthorizeFilter
{
    /**
     * Finish oauth authorization.
     * Builds redirect uri and performs redirect.
     * If user is not logged on, redirect contains the Access Denied Error
     */
    public function finishAuthorization()
    {
        $responseType = $this->getResponseType(); 
        
        if(Yii::$app->request->get('display_response'))
        {
            if (Yii::$app->user->isGuest) 
            {
                echo Json::encode([
                    'success' => 0,
                    'error' => [
                        'message' => 'Access Denied.',
                    ]
                ]);
            }
            else
            {
                $parts = $responseType->getResponseData();
                Yii::$app->response->format = Response::FORMAT_JSON;
                $parts2 = explode('=', $parts['query']);
                echo Json::encode([
                    'success' => 1, 
                    'data' => [
                        'code' => $parts2[1]
                    ]
                ]);
            }
        }
        else
        {
            if (Yii::$app->user->isGuest) {
                $responseType->errorRedirect('The User denied access to your application', Exception::ACCESS_DENIED);
            }
            $parts = $responseType->getResponseData();
            $redirectUri = http_build_url($responseType->redirect_uri, $parts, HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT);

            if (isset($parts['fragment'])) {
                $redirectUri .= '#'.$parts['fragment'];
            }

            \Yii::$app->response->redirect($redirectUri);
        }
    }
}

