<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = 'EconoSim Registry';
//$this->registerJs(
//    "setInterval(function(){ if(!$('#game-list-grid input').is(':focus')) $('#game-list-grid').yiiGridView('applyFilter'); }, 5000)"
//);
?>
<script>  
/**
 * Creates and loads an image element by url.
 * @param  {String} url
 * @return {Promise} promise that resolves to an image element or
 *                   fails to an Error.
 */
var request_image = function(url) {
    return new Promise(function(resolve, reject) {
        var img = new Image();
        img.onload = function() { resolve(img); };
        img.onerror = function() { reject(url); };
        img.src = url + '?random-no-cache=' + Math.floor((1 + Math.random()) * 0x10000).toString(16);
    });
};

/**
 * Pings a url.
 * @param  {String} url
 * @param  {Number} multiplier - optional, factor to adjust the ping by.  0.3 works well for HTTP servers.
 * @return {Promise} promise that resolves to a ping (ms, float).
 */
var ping = function(url, multiplier) {
    if(!url)
        return false;
    
    console.log(url);
    
    return new Promise(function(resolve, reject) {
        var start = (new Date()).getTime();
        var response = function() { 
            var delta = ((new Date()).getTime() - start);
            delta *= (multiplier || 1);
            resolve(delta); 
        };
        request_image(url).then(response).catch(response);
        
        // Set a timeout for max-pings, 5s.
        setTimeout(function() { reject(Error('Timeout')); }, 5000);
    });
};

var hosts = [];

var doPings = function() {
   
    var $spans = $('span.host-value');
   
    if(typeof $spans.eq(0).length >= 1) {
        return false;
    }
    
    var n = 0;
    var $span = $spans.eq(n++);
    
    var then = function(v) {
        $span.html(v);
        $span = $spans.eq(n++);
        if($span.length >= 1)
        {
            p = ping($span.data('host'));
            p.then(then);
        }
    };
    
    var p = ping($span.data('host')).then(then);
};

</script>
<div class="site-index">
    
    <div class="page-header">
        <h1><span class="fa fa-gamepad"></span> EconoSim Games</h1>
    </div>
    
    <?php Pjax::begin([
        'enablePushState' => false,
        'options' => ['id' => 'game-list'],
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['id' => 'game-list-grid'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($Game) {
                    return Html::a($Game->name, $Game->launch_url);
                },
            ],
            [
                'attribute' => 'host_search',
                'format' => 'raw',
                'value' => function($Game) {
                    return Html::a($Game->host, $Game->host);
                },
            ],
            [
                'label' => 'Ping',
                'format' => 'raw',
                'value' => function($Game) {
                    return '<span class="host-value" data-host="' . $Game->host . '"></span>';
                },
            ],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php $this->registerJs("doPings();"); ?>
    <?php Pjax::end(); ?>
    
</div>
