<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalDbVulnExposurePastFourMonthsCollection extends Migration
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
            ->table('external_db_vuln_exposure_past_four_months', function (Blueprint $collection)
            {

            });

        DB::connection('mongodb')->collection('external_db_vuln_exposure_past_four_months')->insert([
            [
                'open_array' => '',
                'close_array' => '',
                'merged_weeks' => '',
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
            ->table('external_db_vuln_exposure_past_four_months', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
