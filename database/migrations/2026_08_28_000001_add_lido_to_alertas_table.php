<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            if (!Schema::hasColumn('alertas', 'lido')) {
                $table->boolean('lido')->default(false)->after('status');
                $table->index(['lido', 'status']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            if (Schema::hasColumn('alertas', 'lido')) {
                $table->dropColumn('lido');
            }
        });
    }
};
