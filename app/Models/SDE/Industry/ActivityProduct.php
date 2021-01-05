<?php

namespace App\Models\SDE\Industry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;

class ActivityProduct extends Model
{
    use HasFactory;

    protected $connection = 'sde';
    protected $table = 'industryActivityProducts';
    protected $primaryKey = 'typeID';

    protected $with = ['productType'];

    public function productType() {
        return $this->belongsTo(Models\SDE\Inventory\Type::class, 'productTypeID', 'typeID');
    }
}
