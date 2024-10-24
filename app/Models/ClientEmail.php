<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEmail extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'client_emails';

    protected $fillable = [
        'name',
        'email',
        'client_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
