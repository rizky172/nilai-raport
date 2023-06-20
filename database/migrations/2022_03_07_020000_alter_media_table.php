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
        Schema::table('media', function (Blueprint $table) {
            $table->index(['fk_id', 'table_name'], 'fk_key_index');

            $table->index('category_id');
            $table->foreign('category_id')
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
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex('fk_key_index');

            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
        });
    }
};
