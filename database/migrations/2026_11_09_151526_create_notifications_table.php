
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User receiving the notification
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Notification content
            $table->string('title', 150);
            $table->text('message');

            // Notification category
            $table->string('type', 50);

            // Read/unread status
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
