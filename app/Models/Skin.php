<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Skin extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'menu_id'];

    // Relationship with Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    
}
