<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wdmg\widgets\Editor;
use wdmg\widgets\SelectInput;
use wdmg\widgets\TagsInput;
use wdmg\widgets\LangSwitcher;
use wdmg\widgets\AliasInput;

/* @var $this yii\web\View */
/* @var $model mecsu\blog\models\Posts */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="blog-form row">
    <div class="col-xs-12 col-sm-12" style="margin-bottom: 10px">
        <?php
            echo LangSwitcher::widget([
                'label' => Yii::t('app/modules/blog', 'Language version'),
                'model' => $model,
                'renderWidget' => 'button-group',
                'createRoute' => 'posts/create',
                'updateRoute' => 'posts/update',
                'supportLocales' => $this->context->module->supportLocales,
                'versions' => (isset($model->source_id)) ? $model->getAllVersions($model->source_id, true) : $model->getAllVersions($model->id, true),
                'options' => [
                    'id' => 'locale-switcher',
                    'class' => 'pull-right'
                ]
            ]);
        ?>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => "addPostForm",
        'enableAjaxValidation' => true,
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-9">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'lang' => ($model->locale ?? Yii::$app->language)]) ?>

        <?= $form->field($model, 'alias')->widget(AliasInput::class, [
            'labels' => [
                'edit' => Yii::t('app/modules/blog', 'Edit'),
                'save' => Yii::t('app/modules/blog', 'Save')
            ],
            'options' => [
                'baseUrl' => ($model->id) ? "https://mecsu.vn/blog/{$model->alias}": "https://mecsu.vn/blog/"
            ]
        ])->label(Yii::t('app/modules/blog', 'Post URL')); ?>

        <?php
            if (isset(Yii::$app->redirects) && $model->url && ($model->status == $model::STATUS_PUBLISHED)) {
                if ($url = Yii::$app->redirects->check($model->url, false)) {
                    echo Html::tag('div', Yii::t('app/modules/redirects', 'For this URL is active redirect to {url}', [
                        'url' => $url
                    ]), [
                        'class' => "alert alert-warning"
                    ]);
                }
            }
        ?>

        <?= $form->field($model, 'excerpt')->textarea(['rows' => 3, 'lang' => ($model->locale ?? Yii::$app->language)]) ?>
        <?php
            // echo $form->field($model, 'content')->widget(Editor::class, [
            //     'options' => [
            //         'id' => 'posts-form-content',
            //         'lang' => ($model->locale ?? Yii::$app->language)
            //     ],
            //     'pluginOptions' => []
            // ])
        ?>

        <?= $form->field($model, 'content')->widget(\dosamigos\tinymce\TinyMce::className(), [
            'options' => [
                'rows' => 6,
                'id' => 'posts-form-content',
            ],
            'language' => 'en',
            //'language' => ($model->locale ?? Yii::$app->language),
            'clientOptions' => [
                'file_picker_types' => 'image',
                'file_picker_callback' => new yii\web\JsExpression("function (callback, value, meta) {
                    var imageInput = $('#posts-temp_image').closest('.image-manager-input');
                    var imageManager = imageInput.find('.open-modal-imagemanager');
                    imageManager.click();
                    $('#posts-temp_image').change(function () {
                        //console.log($('#posts-temp_image_image').attr('src'));
                        callback($('#posts-temp_image_image').attr('src'));
                    });
                }"),
                // 'file_browser_callback' => new yii\web\JsExpression("function(field_name, url, type, win) {
                //     window.open('".yii\helpers\Url::to(['/imagemanager/manager', 'view-mode'=>'iframe', 'select-type'=>'tinymce'])."&tag_name='+field_name,'','width=800,height=540 ,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no');
                // }"),
                'plugins' => [
                    "advlist", "autolink", "lists", "link", "charmap", "preview", "anchor",
                    "searchreplace", "visualblocks", "code", "fullscreen",
                    "insertdatetime", "media", "table", "image", "fullscreen"
                    //"contextmenu", "paste", "print", 
                ],
                'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fullscreen",
                'image_caption' => true,
                'convert_urls' => false,
            ]
        ]);?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <a data-toggle="collapse" href="#postMetaTags">
                        <?= Yii::t('app/modules/blog', "SEO") ?>
                    </a>
                </h6>
            </div>
            <div id="postMetaTags" class="panel-collapse collapse">
                <div class="panel-body">
                    <?= $form->field($model, 'title')->textInput(['lang' => ($model->locale ?? Yii::$app->language)]) ?>
                    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'lang' => ($model->locale ?? Yii::$app->language)]) ?>
                    <?= $form->field($model, 'keywords')->textarea(['rows' => 3, 'lang' => ($model->locale ?? Yii::$app->language)]) ?>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <a data-toggle="collapse" href="#postOptions">
                        <?= Yii::t('app/modules/blog', "Other options") ?>
                    </a>
                </h6>
            </div>
            <div id="postOptions" class="panel-collapse collapse">
                <div class="panel-body">
                    <?= $form->field($model, 'in_sitemap', [
                        'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                    ])->checkbox(['label' => Yii::t('app/modules/blog', '- display in the sitemap')])->label(Yii::t('app/modules/blog', 'Sitemap'))
                    ?>
                    <?= $form->field($model, 'in_rss', [
                        'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                    ])->checkbox(['label' => Yii::t('app/modules/blog', '- display in the rss-feed')])->label(Yii::t('app/modules/blog', 'RSS-feed'))
                    ?>
                    <?= $form->field($model, 'in_turbo', [
                        'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                    ])->checkbox(['label' => Yii::t('app/modules/blog', '- display in the turbo-pages')])->label(Yii::t('app/modules/blog', 'Yandex turbo'))
                    ?>
                    <?= $form->field($model, 'in_amp', [
                        'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                    ])->checkbox(['label' => Yii::t('app/modules/blog', '- display in the AMP pages')])->label(Yii::t('app/modules/blog', 'Google AMP'))
                    ?>
                </div>
            </div>
        </div>
        <div class="hidden-xs hidden-sm">
            <hr/>
            <div class="form-group">
                <?= Html::a(Yii::t('app/modules/blog', '&larr; Back to list'), ['posts/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
                <div class="form-group pull-right">
                <?php if (true || (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
                    'created_by' => $model->created_by,
                    'updated_by' => $model->updated_by
                ])) || !$model->id) { ?>
                    <?php if(empty($model->id)) {
                     echo Html::submitButton(Yii::t('app/modules/blog', 'Save as draft'), ['class' => 'btn btn-warning pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-draft']);
                     echo Html::submitButton(Yii::t('app/modules/blog', 'Publish'), ['class' => 'btn btn-save btn-success pull-right', 'name' => 'save-publish']);
                    } else {
                        if($model->status == $model::STATUS_PUBLISHED) {
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Switch to draft'), ['class' => 'btn btn-warning pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-draft']);
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Save'), ['class' => 'btn btn-save btn-success pull-right', 'name' => 'save-publish']);
                        }
                        else
                        {
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Save as draft'), ['class' => 'btn btn-warning pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-draft']);
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Preview'), ['class' => 'btn btn-info pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-preview']);
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Publish'), ['class' => 'btn btn-save btn-success pull-right', 'name' => 'save-publish']);
                        }
                    }
                    ?>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">

        <?= $form->field($model, 'categories')->checkboxList($categoriesList, [
            'class' => 'list-group',
            'onclick' => "$(this).val( $('input:checkbox:checked').val()); ", // if you use required as a validation rule you will need this for the time being until a fix is in place by yii2
            'item' => function($index, $label, $name, $checked, $value) {
                return '<li class="list-group-item"><label><input type="checkbox" ' . (($checked) ? "checked" : "") . ' name="' . $name . '" value="' . $value . '" tabindex="' . $index . '">&nbsp;' . $label . '</label></li>';
            }
        ]) ?>

        <?= $form->field($model, 'tags')->widget(TagsInput::class, [
            'options' => [
                'id' => 'posts-form-tags',
                'class' => 'form-control',
                'placeholder' => Yii::t('app/modules/blog', 'Type tags...')
            ],
            'pluginOptions' => [
                'autocomplete' => Yii::$app->request->absoluteUrl,
                'format' => 'json',
                'minInput' => 2,
                'maxTags' => 100
            ]
        ]); ?>


        <?php
        echo $form->field($model, 'image')->widget(\app\modules\imagebrowser\components\ImageManagerInputWidget::className(), [
            'aspectRatio' => (16/9), //set the aspect ratio
            'showPreview' => true, //false to hide the preview
            'showDeletePickedImageConfirm' => false, //on true show warning before detach image
        ]);
        // if ($model->image) {
        //     echo '<div class="row">';
        //     echo '<div class="col-xs-12 col-sm-3 col-md-2">' . Html::img($model->getImagePath(true) . '/' . $model->image, ['class' => 'img-responsive']) . '</div>';
        //     echo '<div class="col-xs-12 col-sm-9 col-md-10">' . $form->field($model, 'file')->fileInput() . '</div>';
        //     echo '</div><br/>';
        // } else {
        //     echo $form->field($model, 'file')->fileInput();
        // }
        ?>

        <div class="hide">
            <?php 
                echo $form->field($model, 'temp_image')->widget(\app\modules\imagebrowser\components\ImageManagerInputWidget::className(), [
                    'aspectRatio' => (16/9), //set the aspect ratio
                    'showPreview' => true, //false to hide the preview
                    'showDeletePickedImageConfirm' => false, //on true show warning before detach image
                    // 'options' => [
                    //     'id' => 'temp_image_input_id'
                    // ]
                ]);
            ?>
        </div>

        <div class="hide">
            <?php
                echo $form->field($model, 'status')->widget(SelectInput::class, [
                    'items' => $statusModes,
                    'options' => [
                        'id' => 'posts-form-status',
                        'class' => 'form-control'
                    ]
                ]);
            ?>
        </div>
        <hr/>
        <div class="form-group hidden-md hidden-lg">
            <?= Html::a(Yii::t('app/modules/blog', '&larr; Back to list'), ['posts/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
            <?php if (true || (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
                    'created_by' => $model->created_by,
                    'updated_by' => $model->updated_by
                ])) || !$model->id) { ?>
                    <?php if(empty($model->id)) {
                     echo Html::submitButton(Yii::t('app/modules/blog', 'Save as draft'), ['class' => 'btn btn-warning pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-draft']);
                     echo Html::submitButton(Yii::t('app/modules/blog', 'Publish'), ['class' => 'btn btn-save btn-success pull-right', 'name' => 'save-publish']);
                    } else {
                        if($model->status == $model::STATUS_PUBLISHED) {
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Switch to draft'), ['class' => 'btn btn-warning pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-draft']);
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Save'), ['class' => 'btn btn-save btn-success pull-right', 'name' => 'save-publish']);
                        }
                        else
                        {
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Save as draft'), ['class' => 'btn btn-warning pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-draft']);
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Preview'), ['class' => 'btn btn-info pull-right', 'style' => 'margin-left: 5px', 'name' => 'save-preview']);
                            echo Html::submitButton(Yii::t('app/modules/blog', 'Publish'), ['class' => 'btn btn-save btn-success pull-right', 'name' => 'save-publish']);
                        }
                    }
                    ?>
                <?php } ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerJs(<<< JS
$(document).ready(function() {
    function afterValidateAttribute(event, attribute, messages)
    {
        if (attribute.name && !attribute.alias && messages.length == 0) {
            var form = $(event.target);
            $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serializeArray(),
                }
            ).done(function(data) {
                if (data.alias && form.find('#posts-alias').val().length == 0) {
                    form.find('#posts-alias').val(data.alias);
                    form.find('#posts-alias').change();
                    form.yiiActiveForm('validateAttribute', 'posts-alias');
                }
            }).fail(function () {
                /*form.find('#options-type').val("");
                form.find('#options-type').trigger('change');*/
            });
            return false; // prevent default form submission
        }
    }
    $("#addPostForm").on("afterValidateAttribute", afterValidateAttribute);
});
JS
); ?>