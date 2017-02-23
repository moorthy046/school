<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('invoice_id')->index('fk_invoices1_idx');
			$table->foreign('invoice_id')->references('id')->on('invoices')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->unsignedInteger('user_id')->index('fk_notices_users1_idx');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->string('transaction_id')->nullable();
			$table->string('payment_method');
			$table->float('amount', 6,2);
			$table->string('title');
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
		Schema::table('payments', function(Blueprint $table)
		{
			$table->dropForeign('payments_invoice_id_foreign');
			$table->dropForeign('payments_user_id_foreign');
		});
		Schema::drop('payments');
	}

}
