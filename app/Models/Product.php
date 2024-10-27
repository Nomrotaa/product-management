<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
// app/Models/Product.php

// If there is a `$fillable` array, remove 'image' from it
protected $fillable = ['title', 'description', 'price']; // No 'image'
}
