<?php

use App\Models\Degree;
use App\Models\Subject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldOfStudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_of_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Degree::class);
            $table->foreignIdFor(Subject::class);
            $table->integer('active_from');
            $table->integer('active_to');
            $table->integer('po_version');
            $table->string('name');
            $table->string('name_short');
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
        Schema::dropIfExists('field_of_studies');
    }
}
