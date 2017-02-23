<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sections', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('school_year_id')->index('fk_school_years1_idx');
			$table->foreign('school_year_id', 'departments_school_year_id_foreign')->references('id')->on('school_years')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->unsignedInteger('section_teacher_id')->index('fk_department_teacher1_idx');
			$table->foreign('section_teacher_id', 'departments_department_teacher_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->string('title');
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
		Schema::table('sections', function(Blueprint $table)
		{
			$table->dropForeign('departments_department_teacher_id_foreign');
			$table->dropForeign('departments_school_year_id_foreign');
		});
		Schema::drop('sections');
	}

}
