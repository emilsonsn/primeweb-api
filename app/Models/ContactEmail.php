<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactEmail extends Model
{
    use HasFactory;

    public $table = 'contact_emails';
    public $timestamps= false;

    public $fillable = [
        'contact_id',
        'email',
    ];

    public function contact(){
        return $this->belongsTo(Contact::class);
    }
}
