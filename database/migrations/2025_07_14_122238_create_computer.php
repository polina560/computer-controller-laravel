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
        Schema::create('computer', function (Blueprint $table) {
			$table->id();
			$table->string('computer_name')->nullable();
			$table->string('full_name')->nullable();
			$table->string('ip_address');
			$table->string('mac_address')->nullable();
			$table->integer('status')->default(0)->nullable();
			$table->foreignIdFor(\App\Models\User::class, 'user_id')
                ->nullable()
                ->constrained()
				->cascadeOnDelete()
				->cascadeOnUpdate();
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computer');
    }
};
