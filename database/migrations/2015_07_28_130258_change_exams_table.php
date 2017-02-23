<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeExamsTable extends Migration
{

    public function up()
    {
         Schema::drop('exam_student_group');
         Schema::table('exams', function (Blueprint $table) {
             $table->dropForeign('fk_exams_users1');
         });
         Schema::table('exams', function (Blueprint $table) {
             $table->dropColumn('user_id_teacher');
         });

        Schema::table('exams', function(Blueprint $table)
        {
            $table->unsignedInteger('user_id')->index('fk_exams_users1')->after('id');
            $table->foreign('user_id', 'fk_exams_users1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('subject_id')->index('exams_subjects_notices1_idx')->after('user_id');
            $table->foreign('subject_id', 'exams_subject_id_foreign')->references('id')->on('subjects')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('student_group_id')->index('exams_student_group1_idx')->after('subject_id');
            $table->foreign('student_group_id', 'exams_student_group_id_foreign')->references('id')->on('student_groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('content')->after('title');
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->longText('description')->after('title');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign('fk_exams_users1');
            $table->dropForeign('exams_subject_id_foreign');
            $table->dropForeign('exams_student_group_id_foreign');
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('subject_id');
            $table->dropColumn('student_group_id');
        });
        Schema::table('exams', function(Blueprint $table)
        {
            $table->unsignedInteger('user_id_teacher')->index('fk_exams_users1_idx')->after('id');
            $table->foreign('user_id_teacher', 'fk_exams_users1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
        Schema::create('exam_student_group', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('exam_id')->index('fk_exam_group_exams1_idx');
            $table->foreign('exam_id', 'fk_exam_group_exams1')->references('id')->on('exams')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->unsignedInteger('student_group_id')->index('fk_exam_group_student_groups1_idx');
            $table->foreign('student_group_id', 'fk_exam_group_student_groups1')->references('id')->on('student_groups')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->longText('description')->after('title');
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }

}
