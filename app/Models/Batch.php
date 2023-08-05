<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'camp_id',
    ];

    public function camp(): BelongsTo
    {
        return $this->BelongsTo(Camp::class);
    }

    public function office(): BelongsTo
    {
        return $this->BelongsTo(Office::class);
    }
}
