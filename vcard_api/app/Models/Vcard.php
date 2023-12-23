<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vcard extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'phone_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'photo_url',
        'password',
        'confirmation_code',
        'blocked',
        'balance',
        'max_debit'
    ];

    public function categories(): HasMany {
        return $this->hasMany(Category::class,'vcard','phone_number');
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class,'vcard');
    }
}
