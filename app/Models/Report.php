<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'departure_time',
        'batch_id',
    ];

    public function batch(): BelongsTo
    {
        return $this->BelongsTo(Batch::class);
    }

    public function answers(): HasMany
    {
        return $this->HasMany(Answer::class);
    }
}
