<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('modulecode');
            $table->integer('aktiv_from');
            $table->integer('aktiv_to');
            $table->integer('ects');
            $table->integer('presence_time');
            $table->integer('workload');
            $table->string('rotation'); //Turnus
            $table->string('title_de');
            $table->string('title_en');
            $table->string('composition')->nullable();  //Zusammensetzung
            $table->string('prior_knowledge')->nullable();    //Vorkenntnisse
            $table->string('type')->nullable();     //Art
            $table->string('content')->nullable();  //Inhalte
            $table->string('requirement_creditpoints')->nullable();
            $table->string('requirement_exam')->nullable();
            $table->string('requirement_admission')->nullable();
            $table->string('additional_info')->nullable();
            $table->string('literature')->nullable();
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
        Schema::dropIfExists('modules');
    }
}
