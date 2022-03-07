<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuIdToSmsMessagesInTable extends Migration
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
            $table->String('uu_id', 50)->index()->after('id');

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
            $table->dropColumn('uu_id');
        });
    }
}
