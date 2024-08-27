<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBobot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bobot', function (Blueprint $table) {
            $table->string('kd_jurusan',10);
            $table->string('kd_matapelajaran',10);
            $table->float('bobot');
            $table->timestamps();

            $table->primary(['kd_jurusan','kd_matapelajaran']);

            $table->foreign('kd_jurusan')
                  ->references('kd_jurusan')
                  ->on('jurusan')
                  ->constrained()
                  ->onDelete('no action')
                  ->onUpdate('no action');

            $table->foreign('kd_matapelajaran')
                  ->references('kd_matapelajaran')
                  ->on('matapelajaran')
                  ->constrained()
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bobot');
    }
}
