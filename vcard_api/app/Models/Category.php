<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'vcard',
        'type',
        'name'
    ];

    public function vcard(): BelongsTo {
        return $this->BelongsTo(Vcard::class,'vcard','phone_number');
    }
    public function transaction(): BelongsTo {
        return $this->BelongsTo(Transaction::class,'category_id');
    }
}
