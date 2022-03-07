<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoutTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_activities', function (Blueprint $table) {
            $table->dateTime('logout_time')->nullable()->after('ip_address');
            $table->dropColumn('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_activities', function (Blueprint $table) {
            //
            $table->dropColumn('logout_time');
        });
    }
}
