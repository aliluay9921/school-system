<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('issuer_id');
            $table->integer('type');
            $table->text('body');
            $table->uuid('user_id')->nullable();
            $table->uuid('class_id')->nullable();
            $table->uuid('material_id')->nullable();
            $table->uuid('school_id');
            $table->timestamp('from_time')->nullable();
            $table->timestamp('to_time')->nullable();


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
        Schema::dropIfExists('reports');
    }
}