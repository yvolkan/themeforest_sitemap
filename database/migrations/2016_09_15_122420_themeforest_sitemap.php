<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ThemeforestSitemap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themeforest_sitemap', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('url', '255');
            $table->string('status', '10');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            
            $table->index('url', 'url');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('themeforest_sitemap');
    }
}
