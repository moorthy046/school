<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSemestersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('semesters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('school_year_id')->index('fk_school_years1_idx');
			$table->foreign('school_year_id')->references('id')->on('school_years')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->string('title');
			$table->date('start');
			$table->date('end');
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
		Schema::table('semesters', function(Blueprint $table)
		{
			$table->dropForeign('semesters_school_year_id_foreign');
		});
		Schema::drop('semesters');
	}

}
