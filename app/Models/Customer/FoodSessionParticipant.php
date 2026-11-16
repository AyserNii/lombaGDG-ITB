<?php

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodSessionParticipant extends Model
{
    protected $fillable = [
        'food_session_id',
        'user_id',
        'order_items',
        'estimated_cost',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'estimated_cost' => 'integer',
        ];
    }

    public function foodSession(): BelongsTo
    {
        return $this->belongsTo(BarengMakanModel::class, 'food_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
