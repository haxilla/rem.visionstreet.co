<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Schema\Blueprint;
use Throwable;

try {

    $required = ['id','username','password','password_plain','role'];

    $needsRebuild = !Schema::hasTable('users');
    if (!$needsRebuild) {
        foreach ($required as $col) {
            if (!Schema::hasColumn('users', $col)) {
                $needsRebuild = true;
                break;
            }
        }
    }

    if (!$needsRebuild) {
        return; // table already correct
    }

    DB::transaction(function () {

        DB::statement("
            ALTER TABLE remuserdb.propagents
            MODIFY COLUMN id BIGINT UNSIGNED NOT NULL
        ");

        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('username', 100)->unique();
            $table->string('password_plain', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('role', 20)->default('member');
            $table->rememberToken();
            $table->timestamps();

            $table->primary('id');
            $table->foreign('id')->references('id')->on('propagents')->onDelete('cascade');
        });

        DB::statement("
            INSERT INTO users (id, username, password_plain, role, created_at, updated_at)
            SELECT
                p.id,
                p.xxAgtUname,
                p.agtPswd,
                'member',
                NOW(),
                NOW()
            FROM propagents p
            WHERE p.xxAgtUname IS NOT NULL AND p.xxAgtUname <> ''
              AND p.agtPswd   IS NOT NULL AND p.agtPswd   <> ''
        ");
    });

} catch (Throwable $e) {

    // Log full details
    Log::error('dev_users_rebuild failed', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    // Dump visible error right now (since you're debugging on page load)
    // NOTE: remove this once fixed.
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');

    echo "dev_users_rebuild FAILED\n";
    echo "----------------------------------------\n";
    echo $e::class . "\n";
    echo $e->getMessage() . "\n\n";
    echo "at: " . $e->getFile() . ":" . $e->getLine() . "\n";

    // Stop Laravel from rendering its normal error page
    exit(1);
}