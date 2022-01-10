<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_module', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->integer('module_id')->nullable();
            $table->integer('pnr')->nullable();
            $table->string('description')->nullable();
            $table->string('title');
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
        Schema::dropIfExists('event_module');
    }
}
