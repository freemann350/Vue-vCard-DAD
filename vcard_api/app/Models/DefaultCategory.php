<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultCategory extends Model
{
    use HasFactory;
    protected $table = 'default_categories';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'name',
    ];

}
