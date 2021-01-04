<?php

namespace App\Services;

use App\Models\SDE;

class SdeRepository {

    public function searchModules(string $query) {
        return SDE\Inventory\Type::rigs()->where('typeName', 'like', "%{$query}%")->get();
    }

    public function getTypesByIds(array $ids) {
        return SDE\Inventory\Type::whereIn('typeID', $ids)->get();
    }

    public function getTypeById(int $id) {
        return SDE\Inventory\Type::where('typeID', $id)->first();
    }
}
