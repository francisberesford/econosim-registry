<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Francis Beresford <francis@snapfrozen.com.au>
 */
class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/fortawesome/font-awesome';
    public $css = [
        'css/font-awesome.min.css',
    ];
    public $js = [
    ];
}
