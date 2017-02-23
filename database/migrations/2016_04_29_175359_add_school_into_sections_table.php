<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchoolIntoSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedInteger('school_id')->index('fk_school_sections1_idx')->after('school_year_id');
            $table->foreign('school_id','sections_school_id_foreign')->references('id')->on('schools')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign('sections_school_id_foreign');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('school_id');
        });
    }
}
