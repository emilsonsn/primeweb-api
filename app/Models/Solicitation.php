<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitation extends Model
{
    use HasFactory;

        
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'solicitations';

    protected $fillable = [
        'order_id',
        'solicitation_type',
        'total_value',
        'supplier_id',
        'user_id',
        'construction_id',
        'status',
        'payment_date',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function construction() {
        return $this->belongsTo(Construction::class);
    }
}
