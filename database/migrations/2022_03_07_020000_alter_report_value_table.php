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
        Schema::table('report_value', function (Blueprint $table) {
            $table->index('teacher_id');
            $table->foreign('teacher_id')
            ->references('id')->on('person');

            $table->index('class_id');
            $table->foreign('class_id')
            ->references('id')->on('category');

            $table->index('major_id');
            $table->foreign('major_id')
            ->references('id')->on('category');

            $table->index('lesson_id');
            $table->foreign('lesson_id')
            ->references('id')->on('category');

            $table->index('semester_id');
            $table->foreign('semester_id')
            ->references('id')->on('category');
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
            $table->dropForeign(['teacher_id']);
            $table->dropIndex(['teacher_id']);

            $table->dropForeign(['class_id']);
            $table->dropIndex(['class_id']);

            $table->dropForeign(['major_id']);
            $table->dropIndex(['major_id']);

            $table->dropForeign(['lesson_id']);
            $table->dropIndex(['lesson_id']);

            $table->dropForeign(['semester_id']);
            $table->dropIndex(['semester_id']);
        });
    }
};
