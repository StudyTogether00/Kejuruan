<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_header', function (Blueprint $table) {
            $table->integer('tahun');
            $table->string('nisn',20);
            $table->float('nilai_rata-rata');
            $table->timestamps();

            $table->primary(['tahun','nisn']);

            $table->foreign('nisn')
                  ->references('nisn')
                  ->on('siswa')
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
        Schema::dropIfExists('nilai_header');
    }
}
