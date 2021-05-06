<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecretadmirerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secretadmirer', function (Blueprint $table) {

            $table->index('instagram_id');
            $table->unique('username');
            $table->string('user_id');
            $table->string('media_id');
            $table->string('name');
            $table->string('is_like');
            $table->string('comment_count');
            $table->string('execution_id');

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
        Schema::dropIfExists('secretadmirer');
    }
}
