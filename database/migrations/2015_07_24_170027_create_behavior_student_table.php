<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBehaviorStudentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('behavior_student', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('behavior_id')->index('fk_behavior_student_behaviors1_idx');
			$table->foreign('behavior_id', 'fk_behavior_student_behaviors1')->references('id')->on('behaviors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->unsignedInteger('student_id')->index('fk_behavior_student_students1_idx');
			$table->foreign('student_id', 'fk_behavior_student_students1')->references('id')->on('students')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
		Schema::table('behavior_student', function(Blueprint $table)
		{
			$table->dropForeign('fk_behavior_student_behaviors1');
			$table->dropForeign('fk_behavior_student_students1');
		});
		Schema::drop('behavior_student');
	}

}
