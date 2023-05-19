<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Book;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'customer_id',
        'price_at_purchase',
        'final_cost',
        'order_id',
        'status',
        'quantity',
    ];

    protected $casts = [
        'price_at_purchase' => 'decimal:2',
        'final_cost' => 'decimal:2',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
