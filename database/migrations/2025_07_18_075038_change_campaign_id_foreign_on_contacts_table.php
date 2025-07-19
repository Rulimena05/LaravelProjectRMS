<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Hapus foreign key yang lama dulu
            $table->dropForeign(['campaign_id']);

            // Tambahkan lagi dengan aturan onDelete('cascade')
            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Kembalikan ke aturan lama jika migrasi di-rollback
            $table->dropForeign(['campaign_id']);

            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('set null');
        });
    }
};
