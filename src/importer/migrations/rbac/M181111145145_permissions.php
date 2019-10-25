<?php

namespace ant\importer\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M181111145145_permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\importer\backend\controllers\ImportController::className() => [
				'index' => ['Import book from file', [Role::ROLE_ADMIN]],
				'select-column' => ['Match database column with imported file', [Role::ROLE_ADMIN]],
				'confirm' => ['Confirm to import', [Role::ROLE_ADMIN]],
				'import' => ['Import data', [Role::ROLE_ADMIN]],
				'file-upload' => ['Upload file', [Role::ROLE_ADMIN]],
				'file-delete' => ['Delete uploaded file', [Role::ROLE_ADMIN]],
			],
		];
		
		parent::init();
    }

	public function up()
    {
        //rbac migration
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
