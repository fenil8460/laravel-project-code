<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRunningStatePhoneNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_numbers', function (Blueprint $table) {
            //
            $table->boolean('running_state')->nullable()->after('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phone_numbers', function (Blueprint $table) {
            //
            $table->dropColumn('running_state');
        });
    }
}
