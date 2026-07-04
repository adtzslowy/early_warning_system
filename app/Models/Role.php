<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Role dengan primary key UUID — menyesuaikan skema proyek yang memakai
 * uuid('id') di semua tabel. HasUuids otomatis membangkitkan id saat create.
 */
class Role extends SpatieRole
{
    use HasUuids;
}
