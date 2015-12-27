<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=econosim_game_server',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
    ]
];