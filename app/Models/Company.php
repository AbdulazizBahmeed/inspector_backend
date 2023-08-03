<?php

namespace App\Models;

use App\Models\Camp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    public function camps(): BelongsToMany
    {
        return $this->belongsToMany(Camp::class);
    }
}
