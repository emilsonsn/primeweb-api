<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactPhone extends Model
{
    use HasFactory;

    public $table = 'contact_phones';
    public $timestamps= false;

    public $fillable = [
        'contact_id',
        'phone',
    ];

    public function contact(){
        return $this->belongsTo(Contact::class);
    }
}
