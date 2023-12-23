<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vcard',
        'date',
        'datetime',
        'type',
        'value',
        'old_balance',
        'new_balance',
        'payment_type',
        'payment_reference',
        'pair_transaction',
        'pair_vcard',
        'category_id',
        'description'
    ];

    public function vcard(): HasOne {
        return $this->hasOne(Vcard::class,'phone_number');
    }

    public function pairVcard(): HasOne {
        return $this->hasOne(Vcard::class,'phone_number');
    }

    public function category(): HasOne {
        return $this->hasOne(Category::class,'id');
    }

    public function pairTransaction(): HasOne {
        return $this->hasOne(Transaction::class,'id');
    }
}
