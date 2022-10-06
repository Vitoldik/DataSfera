<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('token', function (Blueprint $table) {
            $table->dropPrimary();
            $table->unique('baseDomain');
            $table->addColumn('string', 'clientToken')->first()->primary();
        });
    }

    public function down()
    {
        Schema::table('token', function (Blueprint $table) {
            //
        });
    }
};
