<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    public function report(): BelongsTo
    {
        return $this->BelongsTo(Report::class);
    }

    public function question(): BelongsTo
    {
        return $this->BelongsTo(Report::class);
    }
}
