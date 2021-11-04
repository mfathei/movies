<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovieGenresRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movie_genres', function(Blueprint $table)
        {
            $table->foreign('movie_id')->references('id')->on('movies')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('genre_id')->references('id')->on('genres')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movie_genres', function(Blueprint $table)
        {
            $table->dropForeign('movie_id');
            $table->dropForeign('genre_id');
        });
    }
}
