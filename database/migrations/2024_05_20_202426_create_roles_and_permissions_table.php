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
        Schema::create('role_and_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('role_and_permissions');
    }
};
