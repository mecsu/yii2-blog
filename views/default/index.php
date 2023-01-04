<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model mecsu\blog\models\Posts */

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_list',
]);

?>