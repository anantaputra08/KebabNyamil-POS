<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['gross_amount', 'order_id', 'user_id' , 'status', 'payment_type'];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
