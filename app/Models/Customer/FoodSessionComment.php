<?php

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodSessionComment extends Model
{
    protected $fillable = [
        'food_session_id',
        'user_id',
        'comment',
    ];

    public function foodSession(): BelongsTo
    {
        return $this->belongsTo(BarengMakanModel::class, 'food_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
