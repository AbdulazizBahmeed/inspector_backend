<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Camp extends Model
{
    use HasFactory;

    public function zone(): BelongsTo
    {
        return $this->BelongsTo(Zone::class);
    }

    public function company(): BelongsTo
    {
        return $this->BelongsTo(company::class);
    }
}
