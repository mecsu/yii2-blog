<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mecsu\blog\models\Posts */

$this->title = Yii::t('app/modules/blog', 'View blog post');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/blog', 'All posts'), 'url' => ['posts/index']];
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
                    if (($postURL = $model->getPostUrl(true, true)) && $model->id) {
                        $output .= '<br/>' . Html::a($model->getUrl(true), $postURL, [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }

                    if (isset(Yii::$app->redirects) && $model->url && ($model->status == $model::STATUS_PUBLISHED)) {
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
                'attribute' => 'image',
                'format' => 'html',
                'value' => function($model) {
                    if ($model->image) {
                        return Html::img($model->image, [
                            'class' => 'img-thumbnail',
                            'style' => 'max-height: 160px'
                        ]);
                    } else {
                        return null;
                    }
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
                'attribute' => 'content',
                'format' => 'html',
                'contentOptions' => [
                    'lang' => ($model->locale ?? Yii::$app->language),
                    'style' => 'display:inline-block;max-height:360px;overflow-x:auto;'
                ]
            ],
            [
                'attribute' => 'categories',
                'label' => Yii::t('app/modules/blog', 'Categories'),
                'format' => 'html',
                'value' => function($data) {
                    if ($categories = $data->getCategories()) {
                        $output = [];
                        foreach ($categories as $category) {
                            $output[] = Html::a($category->name, ['cats/view', 'id' => $category->id]);
                        }
                        return implode(", ", $output);
                    } else {
                        return null;
                    }
                }
            ],
            [
                'attribute' => 'tags',
                'label' => Yii::t('app/modules/blog', 'Tags'),
                'format' => 'html',
                'value' => function($data) {
                    if ($tags = $data->getTags()) {
                        $output = [];
                        foreach ($tags as $tag) {
                            $output[] = Html::a($tag->name, ['tags/view', 'id' => $tag->id]);
                        }
                        return implode(", ", $output);
                    } else {
                        return null;
                    }
                }
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
                'attribute' => 'common',
                'label' => Yii::t('app/modules/blog','Common'),
                'format' => 'html',
                'value' => function($data) {
                    $output = '';
                    if ($data->in_sitemap)
                        $output .= '<span class="fa fa-fw fa-sitemap text-success" title="' . Yii::t('app/modules/blog','Present in sitemap') . '"></span>';
                    else
                        $output .= '<span class="fa fa-fw fa-sitemap text-danger" title="' . Yii::t('app/modules/blog','Not present in sitemap') . '"></span>';

                    $output .= "&nbsp;";

                    if ($data->in_rss)
                        $output .= '<span class="fa fa-fw fa-rss text-success" title="' . Yii::t('app/modules/blog','Present in RSS-feed') . '"></span>';
                    else
                        $output .= '<span class="fa fa-fw fa-rss text-danger" title="' . Yii::t('app/modules/blog','Not present in RSS-feed') . '"></span>';

                    $output .= "&nbsp;";

                    if ($data->in_turbo)
                        $output .= '<span class="fa fa-fw fa-rocket text-success" title="' . Yii::t('app/modules/blog','Present in Yandex.Turbo') . '"></span>';
                    else
                        $output .= '<span class="fa fa-fw fa-rocket text-danger" title="' . Yii::t('app/modules/blog','Not present in Yandex.Turbo') . '"></span>';

                    $output .= "&nbsp;";

                    if ($data->in_amp)
                        $output .= '<span class="fa fa-fw fa-bolt text-success" title="' . Yii::t('app/modules/blog','Present in Google AMP') . '"></span>';
                    else
                        $output .= '<span class="fa fa-fw fa-bolt text-danger" title="' . Yii::t('app/modules/blog','Not present in Google AMP') . '"></span>';

                    return $output;
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
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->status == $data::STATUS_PUBLISHED)
                        return '<span class="label label-success">'.Yii::t('app/modules/blog','Published').'</span>';
                    elseif ($data->status == $data::STATUS_DRAFT)
                        return '<span class="label label-default">'.Yii::t('app/modules/blog','Draft').'</span>';
                    else
                        return $data->status;
                }
            ],
            [
                'attribute' => 'created',
                'label' => Yii::t('app/modules/blog','Created'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['users/view', 'id' => $user->id], [
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
                        $output = Html::a($user->username, ['users/view', 'id' => $user->id], [
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
        <?= Html::a(Yii::t('app/modules/blog', '&larr; Back to list'), ['posts/index'], ['class' => 'btn btn-default pull-left']) ?>
        <?php if (true || Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
                'created_by' => $model->created_by,
                'updated_by' => $model->updated_by
            ])) : ?>
            <div class="form-group pull-right">
                <?= Html::a(Yii::t('app/modules/blog', 'Delete'), ['posts/delete', 'id' => $model->id], [
                    'class' => 'btn btn-delete btn-danger',
                    'data-confirm' => Yii::t('app/modules/blog', 'Are you sure you want to delete this post?'),
                    'data-method' => 'post',
                ]) ?>
                <?= Html::a(Yii::t('app/modules/blog', 'Update'), ['posts/update', 'id' => $model->id], ['class' => 'btn btn-edit btn-primary']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>