<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_activities', function (Blueprint $table) {
            $table->id();
            $table->string('uu_id', 50);
            $table->string('type');
            $table->string('company_id')->nullable();
            $table->string('activity')->nullable();
            $table->integer('phone_id')->nullable();
            $table->string('buy_number')->nullable();
            $table->string('disocnnect')->nullable();
            $table->string('reconnect')->nullable();
            $table->string('message')->nullable();
            $table->string('group')->nullable();
            $table->string('contacts')->nullable();
            $table->string('group_contacts')->nullable();
            $table->string('wallet')->nullable();
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
        Schema::dropIfExists('company_activities');
    }
}
