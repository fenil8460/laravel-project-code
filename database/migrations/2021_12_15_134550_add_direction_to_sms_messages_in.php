<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDirectionToSmsMessagesIn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_messages_in', function (Blueprint $table) {
            //
            $table->string('direction',40)->after('message');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_messages_in', function (Blueprint $table) {
            //
            $table->dropColumn('direction');

        });
    }
}
