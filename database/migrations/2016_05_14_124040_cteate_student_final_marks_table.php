<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CteateStudentFinalMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_final_marks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('student_id')->index('fk_final_marks_students_idx');
            $table->foreign('student_id', 'final_marks_student_id_foreign')->references('id')->on('students')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('school_id')->index('fk_final_marks_schools_idx');
            $table->foreign('school_id', 'final_marks_schools_id_foreign')->references('id')->on('schools')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('school_year_id')->index('fk_final_marks_school_year_idx');
            $table->foreign('school_year_id', 'final_marks_school_years_id_foreign')->references('id')->on('school_years')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('subject_id')->index('fk_final_marks_subjects_idx');
            $table->foreign('subject_id', 'final_marks_subjects_id_foreign')->references('id')->on('subjects')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('mark_value_id')->index('fk_final_marks_mark_values_idx');
            $table->foreign('mark_value_id', 'final_marks_mark_value_id_foreign')->references('id')->on('mark_values')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_final_marks', function (Blueprint $table) {
            $table->dropForeign('final_marks_student_id_foreign');
            $table->dropForeign('final_marks_schools_id_foreign');
            $table->dropForeign('final_marks_school_years_id_foreign');
            $table->dropForeign('final_marks_subjects_id_foreign');
            $table->dropForeign('final_marks_mark_value_id_foreign');
        });
        Schema::drop('student_final_marks');
    }
}
