<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'size',
        'price',
        'toppings'
    ];
    /**
     * @var mixed
     */
    private $name;
    /**
     * @var mixed
     */
    private $size;
    /**
     * @var float|int|mixed
     */
    private $price;
    /**
     * @var mixed|null
     */
    private $toppings;
}
