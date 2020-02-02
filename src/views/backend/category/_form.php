<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\Category;
use DmitriiKoziuk\yii2Shop\helpers\CategoryHelper;
use DmitriiKoziuk\yii2Shop\forms\CategoryInputForm;

/**
 * @var $this              View
 * @var $categoryInputForm CategoryInputForm
 * @var $category          Category
 * @var $categories        Category[]
 */
?>

<div class="category-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($categoryInputForm, 'parent_id')->widget(
                Select2::class,
                [
                    'data' => ArrayHelper::map(
                        CategoryHelper::categoryTreeToList(
                            CategoryHelper::createCategoryTree($categories),
                            $category->id
                        ),
                        'id',
                        'name'
                    ),
                    'options' => [
                        'placeholder' => 'Select a category ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]
            )->label(Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Parent Category')) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($categoryInputForm, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php if (! $category->isNewRecord): ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($categoryInputForm, 'name_on_site')->textInput(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'slug')->textInput(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'meta_title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'meta_description')->textarea(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'description')->textarea(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'is_products_show')->dropDownList([
                Category::IS_PRODUCT_SHOW_FALSE => 'No',
                Category::IS_PRODUCT_SHOW_TRUE => 'Yes',
            ]) ?>

            <?= $form->field($categoryInputForm, 'template_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'filtered_title_template')->textInput(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'filtered_description_template')->textInput(['maxlength' => true]) ?>

            <?= $form->field($categoryInputForm, 'filtered_h1_template')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($category->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $category->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
