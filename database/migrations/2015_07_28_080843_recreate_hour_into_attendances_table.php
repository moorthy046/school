<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RecreateHourIntoAttendancesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('hour')->default(1);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->tinyInteger('hour')->after('date');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('hour');
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('hour')->after('date');
        });
    }

}
