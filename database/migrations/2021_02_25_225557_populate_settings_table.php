<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PopulateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            ['key' => 'app.captcha', 'value' => '0'],
            ['key' => 'app.default_theme', 'value' => 'dark'],
            ['key' => 'app.locale', 'value' => 'en'],
            ['key' => 'app.maintenance', 'value' => '0'],
            ['key' => 'app.registrations', 'value' => '1'],
            ['key' => 'disk.max_disk_quota', 'value' => '0'],
            ['key' => 'key.captcha.site', 'value' => null],
            ['key' => 'key.captcha.private', 'value' => null],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->truncate();
    }
}
