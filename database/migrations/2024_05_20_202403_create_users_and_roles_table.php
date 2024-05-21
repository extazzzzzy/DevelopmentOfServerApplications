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
        Schema::create('users_and_roles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('role_id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent();
            $table->integer('created_by');
            $table->softDeletes()->nullable(); // deleted_at
            $table->integer('deleted_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_and_roles');
    }
};
