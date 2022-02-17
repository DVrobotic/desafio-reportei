<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitHubUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('git_hub_users', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->string('email');
            $table->string('login_id')->nullable()->index();
            $table->json('name');
            $table->json('name_dates');
            $table->string('organization')->index();
            $table->timestamps();
        });

        Schema::table('commits', function (Blueprint $table) {
            $table->foreign('fk_owner_id')->references('login_id')->on('git_hub_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('git_hub_users');
    }
}
