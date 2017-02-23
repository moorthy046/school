<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExamStudentGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
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
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('exam_student_group', function(Blueprint $table)
		{
			$table->dropForeign('fk_exam_group_exams1');
			$table->dropForeign('fk_exam_group_student_groups1');
		});
		Schema::drop('exam_student_group');
	}

}
