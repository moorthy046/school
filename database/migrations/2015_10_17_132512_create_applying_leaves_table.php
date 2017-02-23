<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApplyingLeavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('applying_leaves', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('student_id')->index('fk_applying_leaves_students1_idx');
			$table->foreign('student_id', 'applying_leaves_student_id_foreign')->references('id')->on('students')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->unsignedInteger('parent_id')->index('fk_applying_leaves_users2_idx');
			$table->foreign('parent_id', 'applying_leaves_parent_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->unsignedInteger('school_year_id')->index('fk_school_years_applying_leaves1_idx');
			$table->foreign('school_year_id')->references('id')->on('school_years')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->string('title');
			$table->date('date');
            $table->text('description');
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
		Schema::table('applying_leaves', function(Blueprint $table)
		{
			$table->dropForeign('applying_leaves_student_id_foreign');
			$table->dropForeign('applying_leaves_parent_id_foreign');
		});
		Schema::drop('applying_leaves');
	}

}
