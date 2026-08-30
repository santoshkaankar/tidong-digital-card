<?php

namespace App\Models\Member;

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
        'theme_category_code',
        'variant_number',
        'full_card_no',
        'font_family',
        'icon_style',
        'icon_display_mode',
        'custom_text_color',
        'custom_icon_color',
        'field_toggles',
        'is_active',
    ];

    protected $casts = [
        'field_toggles' => 'array',
        'is_active'     => 'boolean',
    ];

    public function visitingCard()
    {
        return $this->belongsTo(VisitingCard::class, 'visiting_card_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}