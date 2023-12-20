<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    public function shops(){
       return Shop::where("merchant_id", $this->id)->get();
    }
}
