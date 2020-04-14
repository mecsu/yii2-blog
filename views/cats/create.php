<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\blog\models\Categories */

$this->title = Yii::t('app/modules/blog', 'New category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/blog', 'Blog'), 'url' => ['posts/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/blog', 'All categories'), 'url' => ['cats/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?><small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="blog-cats-create">
    <?= $this->render('_form', [
        'module' => $module,
        'model' => $model,
        'parentsList' => $model->getParentsList(false, true),
        'languagesList' => $model->getLanguagesList(false),
    ]); ?>
</div>