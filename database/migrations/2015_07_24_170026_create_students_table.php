<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('students', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('school_year_id')->index('fk_school_years1_idx');
			$table->foreign('school_year_id', 'students_school_year_id')->references('id')->on('school_years')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->unsignedInteger('user_id')->index('fk_users1_idx');
			$table->foreign('user_id', 'students_user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->unsignedInteger('section_id')->index('fk_students_sections1_idx');
			$table->foreign('section_id', 'students_sections1')->references('id')->on('sections')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->boolean('order')->default(1);
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
		Schema::table('students', function(Blueprint $table)
		{
			$table->dropForeign('students_school_year_id');
			$table->dropForeign('students_sections1');
			$table->dropForeign('students_statuses1');
			$table->dropForeign('students_user_id');
		});
		Schema::drop('students');
	}

}
