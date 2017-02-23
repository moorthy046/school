<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStaffAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->date('date');
            $table->text('comment')->nullable();
            $table->unsignedInteger('user_id')->index('fk_staff_attendances_users_idx');
            $table->foreign('user_id', 'staff_attendances_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('school_id')->index('fk_staff_attendances_schools_idx');
            $table->foreign('school_id', 'staff_attendances_school_id_foreign')->references('id')->on('schools')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('school_year_id')->index('fk_staff_attendances_school_year_idx');
            $table->foreign('school_year_id', 'staff_attendances_school_years_id_foreign')->references('id')->on('school_years')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('option_id')->index('fk_staff_attendances_options_idx');
            $table->foreign('option_id', 'staff_attendances_options_id_foreign')->references('id')->on('options')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::table('staff_attendances', function (Blueprint $table) {
            $table->dropForeign('staff_attendances_user_id_foreign');
            $table->dropForeign('staff_attendances_school_id_foreign');
            $table->dropForeign('staff_attendances_school_years_id_foreign');
            $table->dropForeign('staff_attendances_options_id_foreign');
        });
        Schema::drop('staff_attendances');
    }
}
