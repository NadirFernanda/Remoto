<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STATUSES_WITH = ['published', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'em_moderacao', 'negotiating'];
    private const STATUSES_WITHOUT = ['published', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'em_moderacao'];

    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE services DROP CONSTRAINT IF EXISTS services_status_check');
            DB::statement('ALTER TABLE services ALTER COLUMN status TYPE VARCHAR(30)');
            DB::statement('ALTER TABLE services ADD CONSTRAINT services_status_check CHECK (status IN (\'' . implode("', '", self::STATUSES_WITH) . '\'))');
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE services MODIFY COLUMN status ENUM(\'' . implode("', '", self::STATUSES_WITH) . '\') NOT NULL DEFAULT \'published\'');
        }
        // sqlite: no CHECK enforcement needed — column type is untyped
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE services DROP CONSTRAINT IF EXISTS services_status_check');
            DB::statement('ALTER TABLE services ALTER COLUMN status TYPE VARCHAR(30)');
            DB::statement('ALTER TABLE services ADD CONSTRAINT services_status_check CHECK (status IN (\'' . implode("', '", self::STATUSES_WITHOUT) . '\'))');
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE services MODIFY COLUMN status ENUM(\'' . implode("', '", self::STATUSES_WITHOUT) . '\') NOT NULL DEFAULT \'published\'');
        }
    }
};
