<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;
    protected $fillable = ['transaction_id', 'menu_id', 'skin_id', 'meat_id', 'spicy_level_id', 'extras', 'price'];

    protected $casts = [
        'extras' => 'array', // Ubah JSON ke array
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function skin()
    {
        return $this->belongsTo(Skin::class);
    }

    public function meat()
    {
        return $this->belongsTo(Meat::class);
    }

    public function spicyLevel()
    {
        return $this->belongsTo(SpicyLevel::class);
    }
}
