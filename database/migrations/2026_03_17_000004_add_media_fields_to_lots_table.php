<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->string('media_mode')->nullable()->after('auction_duration_minutes');
            $table->string('media_video_url')->nullable()->after('media_mode');
            $table->string('media_live_source')->nullable()->after('media_video_url');
            $table->json('media_images')->nullable()->after('media_live_source');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn([
                'media_mode',
                'media_video_url',
                'media_live_source',
                'media_images',
            ]);
        });
    }
};
