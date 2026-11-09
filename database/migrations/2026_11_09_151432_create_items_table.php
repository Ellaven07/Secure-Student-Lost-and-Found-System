
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // User who submitted the report
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Item category
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            // Lost or found report
            $table->string('type', 10);
            $table->string('item_name', 150);
            $table->text('description')->nullable();

            // Where and when the item was lost or found
            $table->string('location', 255)->nullable();
            $table->date('date_lost_or_found')->nullable();

            // Uploaded item photo
            $table->string('photo_path')->nullable();

            // Report status
            $table->string('status', 30)->default('open');

            // Admin verification
            $table->boolean('is_verified')->default(false);

            $table->timestamps();

            // Improve filtering and searching
            $table->index(['type', 'status']);
            $table->index('date_lost_or_found');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
