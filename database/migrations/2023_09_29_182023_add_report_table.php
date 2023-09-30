<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('subid', 10);
            $table->integer('total_searches');
            $table->integer('monetized_searches');
            $table->integer('ad_clicks');
            $table->date('date');
            $table->double('ctr', 8, 2);
            $table->double('cpc', 8, 2);
            $table->double('rpm', 8, 2);
            $table->double('revenue', 8, 2)->comment(' revenue in (USD)');
            
            $table->timestamps();
            $table->index(['subid', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
