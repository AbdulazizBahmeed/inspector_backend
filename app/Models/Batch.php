<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Batch extends Model
{
    use HasFactory;

    public function camp(): BelongsTo
    {
        return $this->BelongsTo(Camp::class);
    }

    public function office(): BelongsTo
    {
        return $this->BelongsTo(Office::class);
    }

    public function report(): HasOne
    {
        return $this->HasOne(Report::class);
    }
}
