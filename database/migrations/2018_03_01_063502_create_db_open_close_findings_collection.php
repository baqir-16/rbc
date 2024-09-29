<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbOpenCloseFindingsCollection extends Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
            ->table('db_open_close_findings', function (Blueprint $collection)
            {

            });

        DB::connection('mongodb')->collection('db_open_close_findings')->insert([
            [
                'close' => '',
                'open' => '',
                'updated_at' => '',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)
            ->table('db_open_close_findings', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }

}
