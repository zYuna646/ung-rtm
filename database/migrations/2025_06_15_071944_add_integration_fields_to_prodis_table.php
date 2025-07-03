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
        Schema::table('prodis', function (Blueprint $table) {
            $table->string('ami')->nullable()->after('fakultas_id');
            $table->string('survei')->nullable()->after('ami');
            $table->string('akreditasi')->nullable()->after('survei');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prodis', function (Blueprint $table) {
            $table->dropColumn(['ami', 'survei', 'akreditasi']);
        });
    }
};
