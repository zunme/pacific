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
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('recommender')->nullable();
            $table->integer('penalty_total')->default('0');
            $table->integer('penalty_sale')->default('0');
            $table->integer('penalty_purchase')->default('0');
            $table->enum('login_available',['N','Y'])->default('Y');
            $table->enum('islock',['N','Y'])->default('N');
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
