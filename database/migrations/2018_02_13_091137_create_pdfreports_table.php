<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdfreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdfreports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('avatar')->default('avatar.jpg')->nullable();
            $table->integer('c_hours')->nullable();
            $table->string('c_responsible')->nullable();
            $table->string('c_escalation')->nullable();
            $table->integer('h_hours')->nullable();
            $table->string('h_responsible')->nullable();
            $table->string('h_escalation')->nullable();
            $table->integer('m_hours')->nullable();
            $table->string('m_responsible')->nullable();
            $table->string('m_escalation')->nullable();
            $table->integer('l_hours')->nullable();
            $table->string('l_responsible')->nullable();
            $table->string('l_escalation')->nullable();
            $table->text('disclaimer')->nullable();
            $table->string('m_title')->nullable();
            $table->text('m_description')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('pdfreports');
    }
}
