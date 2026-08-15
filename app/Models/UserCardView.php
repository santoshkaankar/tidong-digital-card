<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCardView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visiting_card_id',
        'card_slug',
        'theme_style',
        'field_toggles',
        'is_active',
    ];

    protected $casts = [
        'field_toggles' => 'array',
        'is_active'     => 'boolean',
    ];

    public function visitingCard()
    {
        return $this->belongsTo(VisitingCard::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}