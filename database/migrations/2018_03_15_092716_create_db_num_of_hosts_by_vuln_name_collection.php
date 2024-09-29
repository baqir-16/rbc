<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbNumOfHostsByVulnNameCollection extends Migration
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
            ->table('db_opco_findings', function (Blueprint $collection)
            {

            });

        DB::connection('mongodb')->collection('db_num_of_hosts_by_vuln_name')->insert([
            [
                'num_of_hosts_by_vuln_name' => '',
                'unique_vuln_names' => '',
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
            ->table('db_num_of_hosts_by_vuln_name', function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
