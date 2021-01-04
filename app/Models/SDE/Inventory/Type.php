<?php

namespace App\Models\SDE\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $connection = 'sde';
    protected $table = 'invTypes';
    protected $primaryKey = 'typeID';

    public function scopeRigs($query) {
        return $query->whereIn('groupID', [773, 774, 775, 776, 777, 778, 779, 781, 782, 786, 896, 904, 1232, 1233, 1234, 1308]);
    }

    public function getIconAttribute() {
        return $this->iconID !== null ? "https://images.evetech.net/types/{$this->typeID}/icon" : null;
    }
}
