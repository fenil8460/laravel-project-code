<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivedTimeToSmsMessagesIn extends Migration
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
            $table->string('received_time',40)->after('status');
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
            $table->dropColumn('received_time');
        });
    }
}
