<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_value_detail', function (Blueprint $table) {
            $table->index('report_value_id');
            $table->foreign('report_value_id')
            ->references('id')->on('report_value');

            $table->index('student_id');
            $table->foreign('student_id')
            ->references('id')->on('person');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_value', function (Blueprint $table) {
            $table->dropForeign(['report_value_id']);
            $table->dropIndex(['report_value_id']);

            $table->dropForeign(['student_id']);
            $table->dropIndex(['student_id']);
        });
    }
};
