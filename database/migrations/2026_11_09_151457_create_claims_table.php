
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();

            // Item being claimed
            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            // Student submitting the claim
            $table->foreignId('claimant_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Claim details and ownership proof
            $table->text('proof_description');

            // Claim review
            $table->string('status', 20)->default('pending');
            $table->text('remarks')->nullable();

            // Admin who reviewed the request
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
