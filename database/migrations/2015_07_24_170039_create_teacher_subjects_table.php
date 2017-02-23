<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTeacherSubjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('teacher_subjects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('subject_id')->index('fk_subjects2_idx');
			$table->foreign('subject_id')->references('id')->on('subjects')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->unsignedInteger('teacher_id')->index('fk_absences_users2_idx');
			$table->foreign('teacher_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->unsignedInteger('school_year_id')->index('fk_teacher_subjects_school_years1_idx');
			$table->foreign('school_year_id', 'fk_teacher_subjects_school_years1')->references('id')->on('school_years')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->unsignedInteger('student_group_id')->index('fk_teacher_subjects_student_groups1_idx');
			$table->foreign('student_group_id', 'fk_teacher_subjects_student_groups1')->references('id')->on('student_groups')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
		Schema::table('teacher_subjects', function(Blueprint $table)
		{
			$table->dropForeign('fk_teacher_subjects_school_years1');
			$table->dropForeign('fk_teacher_subjects_student_groups1');
			$table->dropForeign('teacher_subjects_subject_id_foreign');
			$table->dropForeign('teacher_subjects_teacher_id_foreign');
		});
		Schema::drop('teacher_subjects');
	}

}
