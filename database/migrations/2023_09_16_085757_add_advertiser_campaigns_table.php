<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvertiserCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser_campaigns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('advertiser_id')->unsigned();
            $table->string("campaign_name", 200);
            $table->string("subid", 50);
            $table->enum("link_type", ['typein','n2s']);
            $table->text("target_url");
            $table->text("query_string");
            $table->integer('target_count');
            $table->tinyInteger('status')->default(1)->comment("1: active, 2: stopped, 3: completed");
            $table->timestamps();
            $table->foreign('advertiser_id')->references('id')->on('advertisers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertiser_campaigns');
    }
}
