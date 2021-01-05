<?php

namespace App\Models\SDE\Industry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;

class ActivityMaterial extends Model
{
    use HasFactory;

    protected $connection = 'sde';
    protected $table = 'industryActivityMaterials';
    protected $primaryKey = 'typeID';

    protected $with = ['materialType'];

    public function materialType() {
        return $this->belongsTo(Models\SDE\Inventory\Type::class, 'materialTypeID', 'typeID');
    }
}
