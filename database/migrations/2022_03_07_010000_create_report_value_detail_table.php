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
        Schema::create('report_value_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('report_value_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->decimal('value', 15,2)->default(0)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_value_detail');
    }
};
