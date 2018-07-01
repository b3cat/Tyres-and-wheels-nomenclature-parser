<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fields_value_id');
            $table->unsignedInteger('field_id')->nullable();
            $table->string('name_ru-RU')->nullable();
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
        Schema::dropIfExists('fields_values');
    }
}
