<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveCurriculaTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_groups', function (Blueprint $table) {
            $table->dropForeign('fk_student_groups_school_years1');
        });
        Schema::table('student_groups', function (Blueprint $table) {
            $table->dropColumn('school_year_id');
        });
        Schema::table('directions', function (Blueprint $table) {
            $table->dropForeign('directions_curriculum_id_foreign');
        });
        Schema::table('directions', function (Blueprint $table) {
            $table->dropColumn('curriculum_id');
        });
        Schema::drop('curricula');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('directions', function (Blueprint $table) {
            $table->unsignedInteger('curriculum_id')->index('fk_curricula1_idx');
            $table->foreign('curriculum_id')->references('id')->on('curricula')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
        Schema::table('student_groups', function (Blueprint $table) {
            $table->unsignedInteger('school_year_id')->index('fk_student_groups_school_years1_idx');
            $table->foreign('school_year_id', 'fk_student_groups_school_years1')->references('id')->on('school_years')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

}
