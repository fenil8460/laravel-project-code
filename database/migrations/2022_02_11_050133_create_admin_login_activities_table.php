<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminLoginActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_login_activities', function (Blueprint $table) {
            $table->id();
            $table->string('uu_id', 50);
            $table->string('user_type');
            $table->integer('admin_id')->nullable();
            $table->dateTime('login_time');
            $table->dateTime('logout_time')->nullable();
            $table->string('ip_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_login_activities');
    }
}
