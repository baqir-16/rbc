<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Streams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('pmo_id')->default(0);
            $table->unsignedInteger('pmo_comments_id')->default(0);
            $table->timestamp('pmo_assigned_date')->nullable();
            $table->unsignedInteger('tester_id')->default(0);
            $table->unsignedInteger('department')->nullable();
            $table->unsignedInteger('tester_comments_id')->default(0);
            $table->timestamp('tester_assigned_date')->nullable();
            $table->timestamp('tester_scheduled_date')->nullable();
            $table->unsignedInteger('analyst_id')->default(0);
            $table->unsignedInteger('analyst_comments_id')->default(0);
            $table->timestamp('analyst_assigned_date')->nullable();
            $table->unsignedInteger('qa_id')->default(0);
            $table->unsignedInteger('qa_comments_id')->default(0);
            $table->timestamp('qa_assigned_date')->nullable();
            $table->unsignedInteger('hod_id')->default(0);
            $table->unsignedInteger('hod_comments_id')->default(0);
            $table->timestamp('hod_assigned_date')->nullable();
            $table->timestamp('hod_signoff_date')->nullable();
            $table->string('report_filename')->nullable();
            $table->unsignedInteger('status')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('streams');
    }
}
