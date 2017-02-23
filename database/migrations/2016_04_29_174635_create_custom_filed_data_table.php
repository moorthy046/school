<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomFiledDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_field_data', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('fk_custom_field_data_users_idx');
            $table->foreign('user_id', 'custom_field_data_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('custom_field_role_id')->index('fk_custom_field_role_idx');
            $table->foreign('custom_field_role_id', 'custom_field_role_id_foreign')->references('id')->on('custom_field_role')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::table('custom_field_data', function(Blueprint $table)
        {
            $table->dropForeign('custom_field_data_user_id_foreign');
            $table->dropForeign('custom_field_role_id_foreign');
        });
        Schema::drop('custom_field_data');
    }
}
