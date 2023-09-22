<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingPubisherJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_publisher_jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('publisher_job_id')->unsigned();
            $table->string("ip", 20);
            $table->timestamp("created_at");
            $table->foreign('publisher_job_id')->references('id')->on('publisher_jobs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_publisher_jobs');
    }
}
