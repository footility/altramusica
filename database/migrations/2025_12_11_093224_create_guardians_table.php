<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('tax_code')->nullable();
            $table->enum('relationship', ['mother', 'father', 'guardian', 'other'])->nullable();
            $table->string('phone_home')->nullable();
            $table->string('phone_work')->nullable();
            $table->string('cell_1')->nullable();
            $table->string('cell_2')->nullable();
            $table->string('cell_3')->nullable();
            $table->string('cell_4')->nullable();
            $table->string('email_1')->nullable();
            $table->string('email_2')->nullable();
            $table->string('email_3')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Italia');
            $table->boolean('privacy_consent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
