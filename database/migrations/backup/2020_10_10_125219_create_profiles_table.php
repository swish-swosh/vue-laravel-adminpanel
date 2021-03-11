<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('country_id');
            $table->tinyInteger('is_active');
            $table->string('company');
            $table->string('vat');
            $table->string('function');
            $table->string('department');
            $table->string('telephone');
            $table->string('mobile');
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('user_image');
            $table->string('background_image');
            $table->string('about_me');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}