<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model mecsu\blog\models\Posts */

$this->title = Yii::t('app/modules/blog', 'All posts');
$this->params['breadcrumbs'][] = $this->title;

if (isset(Yii::$app->translations) && class_exists('\wdmg\translations\FlagsAsset')) {
    $bundle = \wdmg\translations\FlagsAsset::register(Yii::$app->view);
} else {
    $bundle = false;
}

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="blog-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    $output = Html::tag('strong', $model->name);
                    if (($postURL = $model->getPostUrl(true, true)) && $model->id) {
                        $viewText = Yii::t('app/modules/blog', 'Preview');
                        if($model->status == $model::STATUS_PUBLISHED)
                        {
                            $viewText = Yii::t('app/modules/blog', 'View');
                        }
                        $output .= '<br/>' . Html::a($viewText, $postURL, [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }

                    // if (isset(Yii::$app->redirects) && $model->url && ($model->status == $model::STATUS_PUBLISHED)) {
                    //     if ($url = Yii::$app->redirects->check($model->url, false)) {
                    //         $output .= '&nbsp' . Html::tag('span', '', [
                    //             'class' => "text-danger fa fa-exclamation-circle",
                    //             'data' => [
                    //                 'toggle' => "tooltip",
                    //                 'placement' => "top"
                    //             ],
                    //             'title' => Yii::t('app/modules/redirects', 'For this URL is active redirect to {url}', [
                    //                 'url' => $url
                    //             ])
                    //         ]);
                    //     }
                    // }
                    return $output;
                }
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function($model) {
                    $output = mb_strimwidth(strip_tags($model->title), 0, 64, '…');

                    if (mb_strlen($model->title) > 81)
                        $output .= '&nbsp;' . Html::tag('span', Html::tag('span', '', [
                                'class' => 'fa fa-fw fa-exclamation-triangle',
                                'title' => Yii::t('app/modules/blog','Field exceeds the recommended length of {length} characters.', [
                                    'length' => 80
                                ])
                            ]), ['class' => 'label label-warning']);

                    return $output;
                }
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function($model) {
                    $output = mb_strimwidth(strip_tags($model->description), 0, 64, '…');

                    if (mb_strlen($model->description) > 161)
                        $output .= '&nbsp;' . Html::tag('span', Html::tag('span', '', [
                                'class' => 'fa fa-fw fa-exclamation-triangle',
                                'title' => Yii::t('app/modules/blog','Field exceeds the recommended length of {length} characters.', [
                                    'length' => 160
                                ])
                            ]), ['class' => 'label label-warning']);

                    return $output;
                }
            ],
            /*[
                'attribute' => 'keywords',
                'format' => 'raw',
                'value' => function($model) {
                    $output = mb_strimwidth(strip_tags($model->keywords), 0, 64, '…');

                    if (mb_strlen($model->keywords) > 181)
                        $output .= '&nbsp;' . Html::tag('span', Html::tag('span', '', [
                                'class' => 'fa fa-fw fa-exclamation-triangle',
                                'title' => Yii::t('app/modules/blog','Field exceeds the recommended length of {length} characters.', [
                                    'length' => 180
                                ])
                            ]), ['class' => 'label label-warning']);

                    return $output;
                }
            ],*/

            [
                'attribute' => 'categories',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'categories',
                    'items' => $searchModel->getAllCategoriesList(true),
                    'options' => [
                        'id' => 'posts-list-categories',
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
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
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'tags',
                    'items' => $searchModel->getAllTagsList(true),
                    'options' => [
                        'id' => 'posts-list-tags',
                        'class' => 'form-control'
                    ]
                ]),
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

            // [
            //     'attribute' => 'common',
            //     'label' => Yii::t('app/modules/blog','Common'),
            //     'format' => 'html',
            //     'headerOptions' => [
            //         'class' => 'text-center'
            //     ],
            //     'contentOptions' => [
            //         'class' => 'text-center'
            //     ],
            //     'value' => function($data) {
            //         $output = '';
            //         if ($data->in_sitemap)
            //             $output .= '<span class="fa fa-fw fa-sitemap text-success" title="' . Yii::t('app/modules/blog','Present in sitemap') . '"></span>';
            //         else
            //             $output .= '<span class="fa fa-fw fa-sitemap text-danger" title="' . Yii::t('app/modules/blog','Not present in sitemap') . '"></span>';

            //         $output .= "&nbsp;";

            //         if ($data->in_rss)
            //             $output .= '<span class="fa fa-fw fa-rss text-success" title="' . Yii::t('app/modules/blog','Present in RSS-feed') . '"></span>';
            //         else
            //             $output .= '<span class="fa fa-fw fa-rss text-danger" title="' . Yii::t('app/modules/blog','Not present in RSS-feed') . '"></span>';

            //         $output .= "&nbsp;";

            //         if ($data->in_turbo)
            //             $output .= '<span class="fa fa-fw fa-rocket text-success" title="' . Yii::t('app/modules/blog','Present in Yandex.Turbo') . '"></span>';
            //         else
            //             $output .= '<span class="fa fa-fw fa-rocket text-danger" title="' . Yii::t('app/modules/blog','Not present in Yandex.Turbo') . '"></span>';

            //         $output .= "&nbsp;";

            //         if ($data->in_amp)
            //             $output .= '<span class="fa fa-fw fa-bolt text-success" title="' . Yii::t('app/modules/blog','Present in Google AMP') . '"></span>';
            //         else
            //             $output .= '<span class="fa fa-fw fa-bolt text-danger" title="' . Yii::t('app/modules/blog','Not present in Google AMP') . '"></span>';

            //         return $output;
            //     }
            // ],
            // [
            //     'attribute' => 'locale',
            //     'label' => Yii::t('app/modules/blog','Language versions'),
            //     'format' => 'raw',
            //     'filter' => SelectInput::widget([
            //         'model' => $searchModel,
            //         'attribute' => 'locale',
            //         'items' => $searchModel->getLanguagesList(true),
            //         'options' => [
            //             'id' => 'posts-list-locale',
            //             'class' => 'form-control'
            //         ]
            //     ]),
            //     'headerOptions' => [
            //         'class' => 'text-center',
            //         'style' => 'min-width:96px;'
            //     ],
            //     'contentOptions' => [
            //         'class' => 'text-center'
            //     ],
            //     'value' => function($data) use ($bundle) {

            //         $output = [];
            //         $separator = ", ";
            //         $versions = $data->getAllVersions($data->id, true);
            //         $locales = ArrayHelper::map($versions, 'id', 'locale');

            //         if (isset(Yii::$app->translations)) {
            //             foreach ($locales as $item_locale) {

            //                 $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);

            //                 if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

            //                     if (!($country = $locale['domain']))
            //                         $country = '_unknown';

            //                     $flag = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/' . $country . '.png', [
            //                         'alt' => $locale['name']
            //                     ]);

            //                     if ($data->locale === $locale['locale']) // It`s source version
            //                         $output[] = Html::a($flag,
            //                             [
            //                                 'posts/update', 'id' => $data->id
            //                             ], [
            //                                 'title' => Yii::t('app/modules/blog','Edit source version: {language}', [
            //                                     'language' => $locale['name']
            //                                 ])
            //                             ]
            //                         );
            //                     else  // Other localization versions
            //                         $output[] = Html::a($flag,
            //                             [
            //                                 'posts/update', 'id' => $data->id,
            //                                 'locale' => $locale['locale']
            //                             ], [
            //                                 'title' => Yii::t('app/modules/blog','Edit language version: {language}', [
            //                                     'language' => $locale['name']
            //                                 ])
            //                             ]
            //                         );

            //                 }

            //             }
            //             $separator = "";
            //         } else {
            //             foreach ($locales as $locale) {
            //                 if (!empty($locale)) {

            //                     if (extension_loaded('intl'))
            //                         $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
            //                     else
            //                         $language = $locale;

            //                     if ($data->locale === $locale) // It`s source version
            //                         $output[] = Html::a($language,
            //                             [
            //                                 'posts/update', 'id' => $data->id
            //                             ], [
            //                                 'title' => Yii::t('app/modules/blog','Edit source version: {language}', [
            //                                     'language' => $language
            //                                 ])
            //                             ]
            //                         );
            //                     else  // Other localization versions
            //                         $output[] = Html::a($language,
            //                             [
            //                                 'posts/update', 'id' => $data->id,
            //                                 'locale' => $locale
            //                             ], [
            //                                 'title' => Yii::t('app/modules/blog','Edit language version: {language}', [
            //                                     'language' => $language
            //                                 ])
            //                             ]
            //                         );
            //                 }
            //             }
            //         }


            //         if (is_countable($output)) {
            //             if (count($output) > 0) {
            //                 $onMore = false;
            //                 if (count($output) > 3)
            //                     $onMore = true;

            //                 if ($onMore)
            //                     return join(array_slice($output, 0, 3), $separator) . "&nbsp;…";
            //                 else
            //                     return join($separator, $output);

            //             }
            //         }

            //         return null;
            //     }
            // ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'id' => 'posts-list-status',
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'header' => Yii::t('app/modules/blog','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'buttons'=> [
                    // 'view' => function($url, $data, $key) {
                    //     $output = [];
                    //     $versions = $data->getAllVersions($data->id, true);
                    //     $locales = ArrayHelper::map($versions, 'id', 'locale');
                    //     if (isset(Yii::$app->translations)) {
                    //         foreach ($locales as $item_locale) {
                    //             $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);
                    //             if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

                    //                 if ($data->locale === $locale['locale']) // It`s source version
                    //                     $output[] = Html::a(Yii::t('app/modules/blog','View source version: {language}', [
                    //                         'language' => $locale['name']
                    //                     ]), ['posts/view', 'id' => $data->id]);
                    //                 else  // Other localization versions
                    //                     $output[] = Html::a(Yii::t('app/modules/blog','View language version: {language}', [
                    //                         'language' => $locale['name']
                    //                     ]), ['posts/view', 'id' => $data->id, 'locale' => $locale['locale']]);

                    //             }
                    //         }
                    //     } else {
                    //         foreach ($locales as $locale) {
                    //             if (!empty($locale)) {

                    //                 if (extension_loaded('intl'))
                    //                     $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                    //                 else
                    //                     $language = $locale;

                    //                 if ($data->locale === $locale) // It`s source version
                    //                     $output[] = Html::a(Yii::t('app/modules/blog','View source version: {language}', [
                    //                         'language' => $language
                    //                     ]), ['posts/view', 'id' => $data->id]);
                    //                 else  // Other localization versions
                    //                     $output[] = Html::a(Yii::t('app/modules/blog','View language version: {language}', [
                    //                         'language' => $language
                    //                     ]), ['posts/view', 'id' => $data->id, 'locale' => $locale]);

                    //             }
                    //         }
                    //     }

                    //     if (is_countable($output)) {
                    //         if (count($output) > 1) {
                    //             $html = '';
                    //             $html .= '<div class="btn-group">';
                    //             $html .= Html::a(
                    //                 '<span class="glyphicon glyphicon-eye-open"></span> ' .
                    //                 Yii::t('app/modules/blog', 'View') .
                    //                 ' <span class="caret"></span>',
                    //                 '#',
                    //                 [
                    //                     'class' => "btn btn-block btn-link btn-xs dropdown-toggle",
                    //                     'data-toggle' => "dropdown",
                    //                     'aria-haspopup' => "true",
                    //                     'aria-expanded' => "false"
                    //                 ]);
                    //             $html .= '<ul class="dropdown-menu dropdown-menu-right">';
                    //             $html .= '<li>' . implode("</li><li>", $output) . '</li>';
                    //             $html .= '</ul>';
                    //             $html .= '</div>';
                    //             return $html;
                    //         }
                    //     }
                    //     return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' .
                    //         Yii::t('app/modules/blog', 'View'),
                    //         [
                    //             'posts/view',
                    //             'id' => $data->id
                    //         ], [
                    //             'class' => 'btn btn-link btn-xs'
                    //         ]
                    //     );
                    // },
                    'update' => function($url, $data, $key) {

                        if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && !Yii::$app->user->can('updatePosts', [
                                'created_by' => $data->created_by,
                                'updated_by' => $data->updated_by
                            ])) {
                            return false;
                        }

                        $output = [];
                        $versions = $data->getAllVersions($data->id, true);
                        $locales = ArrayHelper::map($versions, 'id', 'locale');
                        if (isset(Yii::$app->translations)) {
                            foreach ($locales as $item_locale) {
                                $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);
                                if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

                                    if ($data->locale === $locale['locale']) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/blog','Edit source version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['posts/update', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/blog','Edit language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['posts/update', 'id' => $data->id, 'locale' => $locale['locale']]);

                                }
                            }
                        } else {
                            foreach ($locales as $locale) {
                                if (!empty($locale)) {

                                    if (extension_loaded('intl'))
                                        $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                                    else
                                        $language = $locale;

                                    if ($data->locale === $locale) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/blog','Edit source version: {language}', [
                                            'language' => $language
                                        ]), ['posts/update', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/blog','Edit language version: {language}', [
                                            'language' => $language
                                        ]), ['posts/update', 'id' => $data->id, 'locale' => $locale]);

                                }
                            }
                        }

                        if (is_countable($output)) {
                            if (count($output) > 1) {
                                $html = '';
                                $html .= '<div class="btn-group">';
                                $html .= Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span> ' .
                                    Yii::t('app/modules/blog', 'Edit') .
                                    ' <span class="caret"></span>',
                                    '#',
                                    [
                                        'class' => "btn btn-block btn-link btn-xs dropdown-toggle",
                                        'data-toggle' => "dropdown",
                                        'aria-haspopup' => "true",
                                        'aria-expanded' => "false"
                                    ]);
                                $html .= '<ul class="dropdown-menu dropdown-menu-right">';
                                $html .= '<li>' . implode("</li><li>", $output) . '</li>';
                                $html .= '</ul>';
                                $html .= '</div>';
                                return $html;
                            }
                        }
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' .
                            Yii::t('app/modules/blog', 'Edit'),
                            [
                                'posts/update',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs'
                            ]
                        );
                    },
                    'delete' => function($url, $data, $key) {

                        if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && !Yii::$app->user->can('updatePosts', [
                                'created_by' => $data->created_by,
                                'updated_by' => $data->updated_by
                            ])) {
                            return false;
                        }

                        $output = [];
                        $versions = $data->getAllVersions($data->id, true);
                        $locales = ArrayHelper::map($versions, 'id', 'locale');
                        if (isset(Yii::$app->translations)) {
                            foreach ($locales as $item_locale) {
                                $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);
                                if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

                                    if ($data->locale === $locale['locale']) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/blog','Delete source version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['posts/delete', 'id' => $data->id], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/blog', 'Are you sure you want to delete the language version of this post?')
                                        ]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/blog','Delete language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['posts/delete', 'id' => $data->id, 'locale' => $locale['locale']], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/blog', 'Are you sure you want to delete the language version of this post?')
                                        ]);

                                }
                            }
                        } else {
                            foreach ($locales as $locale) {
                                if (!empty($locale)) {

                                    if (extension_loaded('intl'))
                                        $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                                    else
                                        $language = $locale;

                                    if ($data->locale === $locale) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/blog','Delete source version: {language}', [
                                            'language' => $language
                                        ]), ['posts/delete', 'id' => $data->id], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/blog', 'Are you sure you want to delete the language version of this post?')
                                        ]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/blog','Delete language version: {language}', [
                                            'language' => $language
                                        ]), ['posts/delete', 'id' => $data->id, 'locale' => $locale], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/blog', 'Are you sure you want to delete the language version of this post?')
                                        ]);

                                }
                            }
                        }

                        if (is_countable($output)) {
                            if (count($output) > 1) {
                                $html = '';
                                $html .= '<div class="btn-group">';
                                $html .= Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span> ' .
                                    Yii::t('app/modules/blog', 'Delete') .
                                    ' <span class="caret"></span>',
                                    '#',
                                    [
                                        'class' => "btn btn-block btn-link btn-xs dropdown-toggle",
                                        'data-toggle' => "dropdown",
                                        'aria-haspopup' => "true",
                                        'aria-expanded' => "false"
                                    ]);
                                $html .= '<ul class="dropdown-menu dropdown-menu-right">';
                                $html .= '<li>' . implode("</li><li>", $output) . '</li>';
                                $html .= '</ul>';
                                $html .= '</div>';
                                return $html;
                            }
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span> ' .
                            Yii::t('app/modules/blog', 'Delete'),
                            [
                                'posts/delete',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs',
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('app/modules/blog', 'Are you sure you want to delete this post?')
                            ]
                        );
                    }
                ],
            ]
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/blog', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/blog', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/blog', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/blog', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/blog', 'Add new post'), ['posts/create'], ['class' => 'btn btn-add btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
