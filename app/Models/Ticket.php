<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'user_id', 
        'category_id', 
        'status_id',
        'assigned_to',
        'priority'
    ];

    /**
     * Relación: Un ticket pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un ticket tiene una categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación: Un ticket tiene un estado.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Relación: Un ticket puede tener múltiples comentarios.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relación: Un ticket puede estar asignado a un usuario.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
