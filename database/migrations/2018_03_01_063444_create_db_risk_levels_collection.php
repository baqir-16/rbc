<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbRiskLevelsCollection extends Migration
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
            ->table('db_risk_levels', function (Blueprint $collection)
            {

            });

        DB::connection('mongodb')->collection('db_risk_levels')->insert([
            [
                'critical_risk' => '',
                'high_risk' => '',
                'med_risk' => '',
                'low_risk' => '',
                'info_risk' => '',
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
            ->table('db_risk_levels', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
