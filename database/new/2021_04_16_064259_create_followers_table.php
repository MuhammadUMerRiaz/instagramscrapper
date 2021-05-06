<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->index('instagram_id');
            $table->string('execution_id');
            $table->string('username');
            $table->string('user_id');
            $table->string('name');
            $table->string('likes');
            $table->string('comments');
            $table->string('following_back');
            $table->string('is_newfollowers');
            $table->string('is_topfollowers');
            $table->string('is_bestfollowers');
            $table->string('is_worstfollowers');
            $table->string('is_ghostfollowers');
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
        Schema::dropIfExists('followers');
    }
}
