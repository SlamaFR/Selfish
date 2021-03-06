<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string('user_code', 5)->nullable();
            $table->string('media_code', 10)->unique();
            $table->string('media_name');
            $table->bigInteger('media_size');
            $table->string('media_type');
            $table->boolean('visible');
            $table->timestamps();

            $table->foreign('user_code')->references('code')->on('users')->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploads');
    }
}
