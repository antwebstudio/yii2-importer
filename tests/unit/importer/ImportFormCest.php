<?php 
namespace importer;

use Yii;
use UnitTester;
use ant\importer\models\ImportForm;

class ImportFormCest
{
    public function _before(UnitTester $I)
    {
    }
	
	public function testValidate(UnitTester $I) {
		$form = new ImportForm([
			'models' => [
				'default' => ImportFormCestFormModel::className(), // FormModel
			],
			'configs' => [
				'default' => [
					'default.testModel.name',
				],
			],
		]);
		$form->attributes = [
			'file' => ['path' => Yii::getAlias('csv.csv')],
		];
		
		if (!$form->validate()) throw new \Exception(print_r($form->errors, 1));
		$I->assertTrue($form->validate());
	}

    // tests
    public function testProcessForFormModel(UnitTester $I)
    {
		$expectedRecords = 5;
		$beforeCount = ImportFormCestTestModel::find()->count();
		
		$form = new ImportForm([
			'fileBasePath' => '@tests/fixtures/file',
			'models' => [
				'default' => ImportFormCestFormModel::className(), // FormModel
			],
			'configs' => [
				'default' => [
					'default.testModel.name',
				],
			],
		]);
		$form->attributes = [
			'step' => 'confirm',
			'file' => ['path' => Yii::getAlias('csv.csv')],
			'importTo'=> [
				'',
				'',
				'',
				'default.testModel.name',
			],
		];
		if ($form->process() === false) throw new \Exception(print_r($form->errors, 1));
		
		$I->assertEquals($beforeCount + $expectedRecords, ImportFormCestTestModel::find()->count());
    }
	
	public function testGetImportedCount(UnitTester $I) {
		$expectedRecords = 5;
		$beforeCount = ImportFormCestTestModel::find()->count();
		
		$form = new ImportForm([
			'fileBasePath' => '@tests/fixtures/file',
			'models' => [
				'default' => ImportFormCestFormModel::className(), // FormModel
			],
			'configs' => [
				'default' => [
					'default.testModel.name',
				],
			],
		]);
		$form->attributes = [
			'step' => 'confirm',
			'file' => ['path' => Yii::getAlias('csv.csv')],
			'importTo'=> [
				'',
				'',
				'',
				'default.testModel.name',
			],
		];
		if ($form->process() === false) throw new \Exception(print_r($form->errors, 1));
		
		$I->assertEquals($expectedRecords, $form->getImportedCount());
	}
}

class ImportFormCestTestModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function rules() {
		return [
			['name', 'required'],
		];
	}
}

class ImportFormCestFormModel extends \common\base\FormModel {
	public $name;
	
	public function models() {
		return [
			'testModel' => [
				'class' => ImportFormCestTestModel::class,
			],
		];
	}
}
