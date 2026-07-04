<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Permission dengan primary key UUID — menyesuaikan skema proyek yang
 * memakai uuid('id') di semua tabel. HasUuids otomatis membangkitkan id
 * saat create (tanpa ini muncul error 1364: Field 'id' has no default).
 */
class Permission extends SpatiePermission
{
    use HasUuids;
}
