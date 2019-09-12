<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('laravel_search_settings', function (Blueprint $table){
            $table->text('config_settings')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laravel_search_settings');
    }
}