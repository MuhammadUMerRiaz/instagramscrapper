<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->string('instagram_id');
            $table->string('execution_id');
            $table->string('user_id');
            $table->timestamps('postdate');
            $table->string('postnumber');
            $table->string('shortcode');
            $table->string('caption');
            $table->string('likes');
            $table->string('comments');
            $table->string('views');
            $table->string('mediaurl');
            $table->string('mediahdurl');
            $table->string('is_video');
            $table->string('is_image');
            $table->string('media_id');
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
        Schema::dropIfExists('media');
    }
}
