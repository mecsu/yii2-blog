<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mecsu\blog\models\Tags */

$this->title = Yii::t('app/modules/blog', 'View tag');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/blog', 'Blog'), 'url' => ['posts/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/blog', 'All tags'), 'url' => ['tags/index']];
$this->params['breadcrumbs'][] = $this->title;

$bundle = false;
if ($model->locale && isset(Yii::$app->translations) && class_exists('\wdmg\translations\FlagsAsset')) {
    $bundle = \wdmg\translations\FlagsAsset::register(Yii::$app->view);
}

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="blog-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'contentOptions' => [
                    'lang' => ($model->locale ?? Yii::$app->language)
                ],
                'value' => function($model) {
                    $output = Html::tag('strong', $model->name);
                    if (($tagURL = $model->getTagUrl(true, true)) && $model->id) {
                        $output .= '<br/>' . Html::a($model->getUrl(true), $tagURL, [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }

                    if (isset(Yii::$app->redirects) && $model->url) {
                        if ($url = Yii::$app->redirects->check($model->url, false)) {
                            $output .= '&nbsp' . Html::tag('span', '', [
                                'class' => "text-danger fa fa-exclamation-circle",
                                'data' => [
                                    'toggle' => "tooltip",
                                    'placement' => "top"
                                ],
                                'title' => Yii::t('app/modules/redirects', 'For this URL is active redirect to {url}', [
                                    'url' => $url
                                ])
                            ]);
                        }
                    }
                    return $output;
                }
            ],
            [
                'attribute' => 'title',
                'format' => 'ntext',
                'contentOptions' => [
                    'lang' => ($model->locale ?? Yii::$app->language)
                ]
            ],
            [
                'attribute' => 'description',
                'format' => 'ntext',
                'contentOptions' => [
                    'lang' => ($model->locale ?? Yii::$app->language)
                ]
            ],
            [
                'attribute' => 'keywords',
                'format' => 'ntext',
                'contentOptions' => [
                    'lang' => ($model->locale ?? Yii::$app->language)
                ]
            ],
            [
                'attribute' => 'posts',
                'label' => Yii::t('app/modules/blog', 'Posts'),
                'format' => 'html',
                'contentOptions' => [
                    'lang' => ($model->locale ?? Yii::$app->language)
                ],
                'value' => function($data) {
                    if ($posts = $data->posts) {
                        return Html::a(count($posts), ['posts/index', 'tag_id' => $data->id]);
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => 'locale',
                'label' => Yii::t('app/modules/blog','Language'),
                'format' => 'raw',
                'value' => function($data) use ($bundle) {
                    if ($data->locale) {
                        if ($bundle) {
                            $locale = Yii::$app->translations->parseLocale($data->locale, Yii::$app->language);
                            if ($data->locale === $locale['locale']) { // Fixing default locale from PECL intl
                                if (!($country = $locale['domain']))
                                    $country = '_unknown';

                                $flag = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/' . $country . '.png', [
                                    'title' => $locale['name']
                                ]);
                                return $flag . " " . $locale['name'];
                            }
                        } else {
                            if (extension_loaded('intl'))
                                $language = mb_convert_case(trim(\Locale::getDisplayLanguage($data->locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                            else
                                $language = $data->locale;

                            return $language;
                        }
                    }
                    return null;
                }
            ],
            [
                'attribute' => 'created',
                'label' => Yii::t('app/modules/blog','Created'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->created_by) {
                        $output = $data->created_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
            [
                'attribute' => 'updated',
                'label' => Yii::t('app/modules/blog','Updated'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->updatedBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->updated_by) {
                        $output = $data->updated_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],

        ],
    ]); ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/blog', '&larr; Back to list'), ['tags/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?php if (true || Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
                'created_by' => $model->created_by,
                'updated_by' => $model->updated_by
            ])) : ?>
            <div class="form-group pull-right">
                <?= Html::a(Yii::t('app/modules/blog', 'Delete'), ['tags/delete', 'id' => $model->id], [
                    'class' => 'btn btn-delete btn-danger',
                    'data-confirm' => Yii::t('app/modules/blog', 'Are you sure you want to delete this tag?'),
                    'data-method' => 'post',
                ]) ?>
                <?= Html::a(Yii::t('app/modules/blog', 'Update'), ['tags/update', 'id' => $model->id], ['class' => 'btn btn-edit btn-primary']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>