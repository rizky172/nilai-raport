<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('person', function (Blueprint $table) {
            $table->index('person_category_id');
            $table->foreign('person_category_id')
            ->references('id')->on('category');

            $table->index('major_id');
            $table->foreign('major_id')
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
        Schema::table('person', function (Blueprint $table) {
            $table->dropForeign(['person_category_id']);
            $table->dropIndex(['person_category_id']);

            $table->dropForeign(['major_id']);
            $table->dropIndex(['major_id']);
        });
    }
}
