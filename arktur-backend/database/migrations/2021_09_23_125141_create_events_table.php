<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->integer('vnr');
            $table->integer('semester');
            $table->string('title');
            $table->boolean('active');
            $table->integer('sws')->nullable();
            $table->string('type');
            $table->string('targets')->nullable();
            $table->integer('rotation')->nullable();
            $table->boolean('changed')->default(true);
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
        Schema::dropIfExists('events');
    }
}
