<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
//            $table->bigInteger('phone')->unique()->after('email');
            $table->enum('user_type', ['admin', 'publisher'])->after('password');
            $table->text('address')->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
//            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('user_type');
        });
    }
}
