<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
            $table->integer('price');
            $table->integer('responsible_user_id');
            $table->integer('group_id');
            $table->integer('status_id');
            $table->integer('pipeline_id');
            $table->integer('loss_reason_id')->nullable();
            $table->integer('source_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('closest_task_at')->nullable();
            $table->boolean('is_deleted');
            $table->integer('score')->nullable();
            $table->integer('account_id');
            $table->integer('company_id')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead');
    }
};
