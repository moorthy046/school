<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudentIdIntoStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_no')->nullable()->after('order');
            $table->unsignedInteger('school_id')->index('fk_students_schools1_idx')->after('student_no');
            $table->foreign('school_id','students_school_id_foreign')->references('id')->on('schools')->onUpdate('RESTRICT')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diaries', function (Blueprint $table) {
            $table->dropForeign('students_school_id_foreign');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('student_no');
            $table->dropColumn('school_id');
        });
    }
}
