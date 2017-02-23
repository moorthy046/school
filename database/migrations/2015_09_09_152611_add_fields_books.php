<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFieldsBooks extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('internal')->after('id');
            $table->string('publisher')->after('title');
            $table->string('version')->after('publisher');

        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('internal');
            $table->dropColumn('publisher');
            $table->dropColumn('version');
        });
    }

}
