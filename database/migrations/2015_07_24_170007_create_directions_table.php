<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDirectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('directions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('curriculum_id')->index('fk_curricula1_idx');
			$table->foreign('curriculum_id')->references('id')->on('curricula')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->string('title');
			$table->boolean('duration');
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
		Schema::table('directions', function(Blueprint $table)
		{
			$table->dropForeign('directions_curriculum_id_foreign');
		});
		Schema::drop('directions');
	}

}
