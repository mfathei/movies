<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('adult')->default(false);
            $table->string('backdrop_path')->nullable();
            $table->string('homepage')->nullable();
            $table->string('imdb_id')->nullable();
            $table->string('original_language')->nullable();
            $table->string('original_title');
            $table->string('overview');
            $table->float('popularity')->default(0);
            $table->string('poster_path')->nullable();
            $table->bigInteger('budget')->nullable();
            $table->date('release_date');
            $table->bigInteger('revenue')->nullable();
            $table->bigInteger('runtime')->nullable();
            $table->string('status');
            $table->string('tagline')->nullable();
            $table->string('title');
            $table->boolean('video')->default(false);
            $table->float('vote_average')->default(0);
            $table->bigInteger('vote_count')->default(0);
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
        Schema::dropIfExists('movies');
    }
}
