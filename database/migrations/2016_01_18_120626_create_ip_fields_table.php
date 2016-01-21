<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('sort')->unsigned()->default(0);
            $table->string('title');
            $table->text('bindings');
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
        Schema::drop('ip_fields');
    }
}
