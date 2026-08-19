<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // Identifiant unique persistant (hardware ou UUID)
            $table->string('platform')->default('android'); // 'android', 'ios_pwa', 'ios_app', 'web'
            $table->string('device_model')->nullable(); // ex: 'Samsung Galaxy A52', 'iPhone iOS 17 (PWA)'
            $table->string('os_version')->nullable(); // ex: 'Android 14', 'iOS 17.5'
            $table->string('app_version')->nullable(); // ex: '1.1.0'
            $table->boolean('is_pwa')->default(false);
            $table->string('fcm_token')->nullable()->index(); // Token push FCM ou WebPush
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_devices');
    }
};
