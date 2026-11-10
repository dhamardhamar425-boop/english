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
        if (Schema::hasColumn('registrations', 'major')) {
            Schema::table('registrations', function (Blueprint $table): void {
                $table->dropColumn('major');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('registrations', 'major')) {
            Schema::table('registrations', function (Blueprint $table): void {
                $table->string('major')->after('class');
            });
        }
    }
};
