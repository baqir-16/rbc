<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalDbNumOfHostsByCatCollection extends Migration
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
            ->table('external_db_num_of_hosts_by_cat', function (Blueprint $collection)
            {

            });

        DB::connection('mongodb')->collection('external_db_num_of_hosts_by_cat')->insert([
            [
                'hosts_per_cat_array' => '',
                'cat_array' => '',
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
            ->table('external_db_num_of_hosts_by_cat', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
