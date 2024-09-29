<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbByCategoryCollection extends Migration
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
            ->table('db_by_category', function (Blueprint $collection)
            {

            });

//        DB::connection('mongodb')->collection('db_by_category')->insert([
//            [
//                'opco_array' => '',
//                'opco_risk_array' => '',
//                'updated_at' => '',
//            ],
//        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)
            ->table('db_by_category', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
