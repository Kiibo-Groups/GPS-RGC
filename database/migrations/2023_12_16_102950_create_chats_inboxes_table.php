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
        Schema::create('chats_inboxes', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); 
            $table->string('cc');
            $table->string('subject');
            $table->longText('message');
            $table->integer('ready');
            $table->integer('status');
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
        Schema::dropIfExists('chats_inboxes');
    }
};
