<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public function house(){
        return $this->belongsTo(House::class);
    }

    protected $fillable = [
        'image_path',
        'type',
        'house_id'
    ];
}
