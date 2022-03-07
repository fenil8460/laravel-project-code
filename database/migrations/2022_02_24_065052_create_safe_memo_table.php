<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafeMemoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('safe_memo', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->text('reason')->nullable();
            $table->integer('safe_spam')->nullable(); 
            $table->integer('entry_by_id')->nullable(); 
            $table->string('entry_by_nick_name',45)->nullable(); 
            $table->boolean('followup')->nullable(); 
            $table->string('approve_reason',200)->nullable(); 
            $table->string('approve_for',200)->nullable(); 
            $table->string('ip_address',36)->nullable(); 
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
        Schema::dropIfExists('safe_memo');
    }
}
