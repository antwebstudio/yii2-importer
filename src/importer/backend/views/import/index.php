<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use trntv\filekit\widget\Upload;
?>

<?php if (!\Yii::$app->request->post()): ?>
    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'step')->hiddenInput(['value' => 'read'])->label(false) ?>

        <?= $form->field($model, 'file')->widget(
            Upload::classname(),
            [
                'url' => ['file-upload'],
            ]
        ) ?>

        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php elseif ($model->step == 'read'): ?>
    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'step')->hiddenInput(['value' => 'confirm'])->label(false) ?>

        <div style="display: none;">
            <?= $form->field($model, 'file')->widget(
                Upload::classname(),
                [
                    'url' => ['file-upload'],
                ]
            ) ?>
        </div>
        <?php
            $columnCount = count(current($model->readAsDataProvider()->getModels()));
            
            for ($i = 0; $i < $columnCount; $i++ ) {
                $widget = $form->field($model, 'importTo['.$i.']')->dropDownlist($model->getImportToDropdown(), [
                    'prompt' => '',
                ]);
                $columns[] = [
                    'header' => $model->getImportedDataHeader($i).'<br/>'.$widget,
                    'attribute' => $i,
                ];
            }
        ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $columns,
        ]) ?>

        <?= Html::submitButton('Import', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php elseif ($model->step == 'confirm'): ?>
    <?php if ($model->lastModel->hasErrors()): ?>
        <?= \yii\bootstrap\Alert::widget([
            'body' => Html::errorSummary($model->lastModel),
            'options' => [
                'class' => 'alert-error',
            ],
        ]) ?>
    <?php else: ?>
        <?= \yii\bootstrap\Alert::widget([
            'body' => 'Success',
            'options' => [
                'class' => 'alert-success',
            ],
        ]) ?>
    <?php endif ?>
    <div>Total data count: <?= $model->dataProvider->totalCount ?></div>
    <div>Imported count: <?= $model->importedCount ?></div>
    <?= Html::a('OK', ['/importer/import', 'type' => $model->type], ['class' => 'btn btn-primary']) ?>
<?php endif ?>