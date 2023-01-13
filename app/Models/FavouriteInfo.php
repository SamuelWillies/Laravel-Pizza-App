<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteInfo extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = 'favouriteinfo';

    protected $fillable = [
        'deals',
        'delivery',
        'userId'
    ];
}
