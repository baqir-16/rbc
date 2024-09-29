<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalDbOpenCollection extends Migration
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
            ->table('external_db_open', function (Blueprint $collection)
            {

            });

        DB::connection('mongodb')->collection('external_db_open')->insert([
            [
                'critical_risk' => '',
                'high_risk' => '',
                'med_risk' => '',
                'low_risk' => '',
                'pending_rem_c' => '',
                'pending_rem_h' => '',
                'pending_rem_m' => '',
                'pending_rem_l' => '',
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
            ->table('external_db_open', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
