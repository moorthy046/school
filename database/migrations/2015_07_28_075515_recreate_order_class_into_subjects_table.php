<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RecreateOrderClassIntoSubjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('class');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->tinyInteger('order')->default(1)->after('title');
            $table->tinyInteger('class')->default(1)->after('order');
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
            $table->dropColumn('order');
            $table->dropColumn('class');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('order')->nullable()->after('title');
            $table->boolean('class')->after('order');
        });
    }

}
