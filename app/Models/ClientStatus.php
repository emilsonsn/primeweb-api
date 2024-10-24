<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientStatus extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    public $table = 'client_status';

    protected $fillable = [
        'status',
        'date',
        'client_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
