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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('role');
            $table->string('logo',150);
            $table->string('logo_top',150);
            $table->string('logo_top_sm',150);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('whatsapp_1',150)->nullable();
            $table->string('whatsapp_2',150)->nullable();
            $table->longtext('perm')->nullable();
            $table->string('username');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('shw_password');
            $table->integer('status')->default(0);
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
};
