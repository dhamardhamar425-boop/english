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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('full_name');
            $table->string('nis')->unique();
            $table->string('nik')->unique();
            $table->string('gender', 20);
            $table->string('class', 30)->index();
            $table->string('phone', 25);
            $table->text('address');
            $table->text('reason');
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('menunggu')->index();
            $table->string('final_result', 20)->nullable()->index();
            $table->text('admin_notes')->nullable();
            $table->timestamp('registered_at')->index();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
