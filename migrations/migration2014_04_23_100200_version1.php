<?php
namespace Addon\Directadmin\Migrations;

use \App\Libraries\BaseMigration;

class Migration2014_04_23_100200_version1 extends BaseMigration
{
    public function up($addon_id)
    {
        // Server Module
        $module = new \ServerModule();
        $module->name = 'DirectAdmin';
        $module->slug = 'directadmin';
        $module->addon_id = $addon_id;
        $module->save();
    }

    public function down($addon_id)
    {
        \ServerModule::where('addon_id', '=', $addon_id)->delete();
    }
}
