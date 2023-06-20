<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_category_id')->unsigned();
            $table->integer('major_id')->unsigned()->nullable();
            $table->string('ref_no', 64)->unique();
            $table->string('nis', 64)->nullable();
            $table->string('nip', 64)->nullable();
            $table->string('name', 64);
            $table->string('email', 64)->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('address')->nullable();
            $table->string('notes')->nullable();

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
        Schema::dropIfExists('person');
    }
}