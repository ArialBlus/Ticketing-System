<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relación: Una categoría puede tener muchos tickets.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
