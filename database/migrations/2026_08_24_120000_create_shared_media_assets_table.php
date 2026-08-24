<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tracks Cloudinary assets that have been attached to more than one
        // field via the shared image-library picker. Once an asset is known
        // to be (potentially) referenced in more than one place, it must
        // never be auto-deleted from Cloudinary just because one of those
        // places replaces or clears its image — see
        // App\Livewire\Uploads\SecureImageUpload::deleteStoredPath().
        Schema::create('shared_media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_media_assets');
    }
};
