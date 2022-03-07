<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsMessagesOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_messages_out', function (Blueprint $table) {
            $table->id();
            $table->string('bandwidth_referrence_id')->index();
            $table->integer('created_by_id');
            $table->integer('company_id');
            $table->integer('phone_number_id');
            $table->string('to_number');
            $table->text('message');
            $table->boolean('is_group');
            $table->integer('status');
            $table->softDeletes();
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
        Schema::dropIfExists('sms_messages_out');
    }
}
