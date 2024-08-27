<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailNilai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_nilai', function (Blueprint $table) {
            $table->integer('tahun');
            $table->string('nisn',20);
            $table->string('kd_matapelajaran',10);
            $table->float('nilai');
            $table->timestamps();

            $table->primary(['tahun','nisn','kd_matapelajaran']);

            $table->foreign(['tahun','nisn'])
                  ->references(['tahun','nisn'])
                  ->on('nilai_header')
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
        Schema::dropIfExists('detail_nilai');
    }
}
