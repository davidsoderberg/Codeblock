<?php namespace App\Services;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\CRepository;
use Illuminate\Support\Facades\Artisan;

class Installer
{

    public static function testMail(){
        $mail = new CRepository();
        $data = array('subject' => 'Test mail', 'body' => 'This is a mail to test mail configuration.');
        $emailInfo = array('subject' => 'Test mail');
        if (!$mail->sendEmail('emails.notification', $emailInfo, $data)) {
            return false;
        }
        return true;
    }

    public static function testGithub(){
        $github = new Github();
        if (!$github->isToken(env('GITHUB_TOKEN', null))) {
            return false;
        }
        return true;
    }

    public static function testDatabase(){
        try {
            DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public static function runMigration(){
        try {
            Artisan::call('migrate');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public static function runSeed(){
        try {
            Artisan::call('db:seed');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public static function createPermissions(){
        try {
            Artisan::call('InsertPermissions');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}