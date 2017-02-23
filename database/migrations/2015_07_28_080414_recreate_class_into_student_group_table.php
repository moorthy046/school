<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RecreateClassIntoStudentGroupTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('class');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->tinyInteger('class')->after('title');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('class');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('class')->after('order');
        });
    }

}
