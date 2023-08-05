<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camp extends Model
{
    use HasFactory;

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function zone(): BelongsTo
    {
        return $this->BelongsTo(Zone::class);
    }

    public function offices(): HasMany
    {
        return $this->HasMany(Office::class);
    }
}
