<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendances', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('semester_id')->index('fk_semesters2_idx');
			$table->foreign('semester_id')->references('id')->on('semesters')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->unsignedInteger('subject_id')->index('fk_subject_absence2_idx');
			$table->foreign('subject_id')->references('id')->on('subjects')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->unsignedInteger('teacher_id')->index('fk_absences_users2_idx');
			$table->foreign('teacher_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->unsignedInteger('student_id')->index('fk_attendance_students1_idx');
			$table->foreign('student_id', 'fk_attendance_students1')->references('id')->on('students')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->date('date');
			$table->boolean('hour');
			$table->text('note');
			$table->boolean('justified')->nullable();
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
		Schema::table('attendances', function(Blueprint $table)
		{
			$table->dropForeign('attendances_semester_id_foreign');
			$table->dropForeign('attendances_subject_id_foreign');
			$table->dropForeign('attendances_teacher_id_foreign');
			$table->dropForeign('fk_attendances_students1');
		});
		Schema::drop('attendances');
	}

}
