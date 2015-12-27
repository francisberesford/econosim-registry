<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%games}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $ip_address
 * @property integer $port
 */
class Game extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%games}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'ip_address'], 'required'],
            [['id', 'ip_address'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['launch_url'], 'string', 'max' => 255],
        ];
    }
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'ip_address' => Yii::t('app', 'IP Address'),
            'host_search' => Yii::t('app', 'Host'),
        ];
    }
    
    public function getHost()
    {
        $parts = parse_url($this->launch_url);
        return isset($parts['host']) ? $parts['scheme'] . '://' . $parts['host'] : null;
    }
    
    public function getWebsocket_url()
    {
        $ip = long2ip($this->ip_address);
        return "ws://$ip:$this->port";
    }
    
    public static function removeInactive()
    {
        foreach(self::find()->all() as $Game)
        {
            $ip = long2ip($Game->ip_address);
            if (!$fs = @fsockopen($ip, $Game->port)) {
                $Game->delete();
            }
            else {
                @fclose($fs); //close connection
            }
        }
    }
}
