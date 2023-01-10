<?php

use wdmg\helpers\StringHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model mecsu\blog\models\Posts */

$this->title = Yii::t('app/modules/blog', 'Updating post: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/blog', 'All posts'), 'url' => ['posts/index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::stringShorter($model->name, 64), 'url' => ['posts/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/blog', 'Updating');
?>
<?php if (true || Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
    'created_by' => $model->created_by,
    'updated_by' => $model->updated_by
])) : ?>
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?>
            <?php
                    if ($model->status == $model::STATUS_PUBLISHED)
                    echo '<small class="label label-success">'.Yii::t('app/modules/blog','Published').'</small>';
                elseif ($model->status == $model::STATUS_DRAFT)
                    echo '<small class="label label-default">'.Yii::t('app/modules/blog','Draft').'</small>';
                else
                    echo $model->status;
            ?>
            <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
        </h1>
    </div>
    <div class="blog-update">
        <?= $this->render('_form', [
            'module' => $module,
            'model' => $model,
            'categoriesList' => $model->getAllCategoriesList(false),
            'tagsList' => $model->getTagsList(),
            'statusModes' => $model->getStatusesList(),
        ]); ?>
    </div>
<?php else: ?>
    <div class="page-header">
        <h1 class="text-danger"><?= Yii::t('app/modules/blog', 'Error {code}. Access Denied', [
                'code' => 403
            ]) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="blog-tags-update-error">
        <blockquote>
            <?= Yii::t('app/modules/blog', 'You are not allowed to view this page.'); ?>
        </blockquote>
    </div>
<?php endif; ?>