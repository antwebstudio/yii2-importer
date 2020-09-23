<?php
namespace ant\importer\components;

use ant\helpers\ArrayHelper;

class Version2ARImportStrategy extends \ruskid\csvimporter\ARImportStrategy {
    public $lastModel;
    public $initModel;
    public $importForm;

    public function import(&$data) {
        $importedPks = [];
        foreach ($data as $row) {
            $skipImport = isset($this->skipImport) ? call_user_func_array($this->skipImport, [$row]) : false;
            if (!$skipImport) {
                /* @var $model \yii\db\ActiveRecord */
                $uniqueAttributes = [];
                $attributes = [];

                foreach ($this->configs as $config) {
                    if (isset($config['attribute']) && trim($config['attribute']) != '') {
                        $value = call_user_func_array($config['value'], [$row]);

                        //Create array of unique attributes
                        if (isset($config['unique']) && $config['unique']) {
                            $uniqueAttributes[$config['attribute']] = $value;
                        }

                        //Set value to the model
						if (isset($value)) {
							$currentValue = (array) ArrayHelper::getValue($attributes, $config['attribute']);
							if (is_array($value)) $value = ArrayHelper::merge($currentValue, $value);
							ArrayHelper::setValue($attributes, $config['attribute'], $value);
						}
                    }
                }

                if (is_callable($this->initModel)) {
                    $this->lastModel = call_user_func_array($this->initModel, [$attributes, $this]);
                } else {   
                    $this->lastModel = \Yii::createObject($this->className);
                }
                
                foreach($attributes as $attribute => $value) {
                    $this->lastModel->{$attribute} = $value;
                }

                //Check if model is unique and saved with success
                if (!$this->lastModel->validate()) throw new \Exception(print_r($this->lastModel->errors, 1).print_r($row, 1));

                if ($this->isActiveRecordUnique($uniqueAttributes) && $this->lastModel->save()) {
					if (isset($this->lastModel->primaryKey)) {
						$importedPks[] = $this->lastModel->primaryKey;
					}
                } else {
                    throw new \Exception('Failed import. '.\yii\helpers\Html::errorSummary($this->lastModel));
                }
            }
        }
        return $importedPks;
    }
    
    private function isActiveRecordUnique($attributes) {
        /* @var $class \yii\db\ActiveRecord */
        $class = get_class($this->lastModel);
        return empty($attributes) ? true :
                !$class::find()->where($attributes)->exists();
    }
}