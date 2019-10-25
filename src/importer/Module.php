<?php

namespace ant\importer;

/**
 * importer module definition class
 */
class Module extends \yii\base\Module
{

    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\ConfigurableModuleBehavior',
                'formModels' => [
                    'import' => [
                        'class' => \ant\importer\models\ImportForm::class,
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
