<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTablePublisherJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('publisher_id')->unsigned();
            $table->bigInteger('advertiser_campaign_id')->unsigned();
            $table->tinyInteger('status')->default(1)->comment("1: active, 0: inactive");
            $table->string("proxy_url", 100)->unique();
            $table->integer("target_count");
            $table->integer("tracking_count");
            $table->timestamps();
            $table->foreign('publisher_id')->references('id')->on('users')->comment("pusher_id will be user_id of user_type=publisher");
            $table->foreign('advertiser_campaign_id')->references('id')->on('advertiser_campaigns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publisher_jobs');
    }
}
