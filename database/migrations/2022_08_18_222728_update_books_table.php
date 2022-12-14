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
        Schema::table('books', function (Blueprint $table) {
            $table->string('subtitle')->nullable();
            $table->string('authors')->nullable();
            $table->string('publishers')->nullable();
            $table->string('publish_date')->nullable();
            $table->string('subjects')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('public_comment')->nullable();
            $table->integer('isWishlistItem')->default(0);
            $table->string('book_file_path')->nullable();
            $table->integer('has_pdf')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
};
