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
            $table->string('login_id');
            $table->json('name');
            $table->json('name_dates');
            $table->string('organization')->index();
            $table->timestamps();
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
