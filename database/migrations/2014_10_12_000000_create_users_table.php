<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id');
            $table->string('full_name');
            $table->string('user_name')->unique();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('gender');
            $table->date('birth_day')->nullable();
            $table->double('discount_value')->nullable();
            $table->uuid('class_id')->nullable();
            $table->string('parent_job')->nullable();
            $table->integer('user_type');
            $table->double('salary')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}