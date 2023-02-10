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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->enum('document_type',['NATIONAL_ID','PASSPORT']);
            $table->string('document_number')->unique();
            $table->enum('nationality',["KENYAN"]);
            $table->string('password');
            $table->enum('status',['PENDING','ACTIVE','SUSPENDED','REJECTED'])->default('PENDING');
            $table->enum('iprs_status',['SUCCESS','FAILED','PENDING'])->default('SUCCESS');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_phone_number_confirmed')->default(false);
            $table->boolean('is_email_address_confirmed')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
