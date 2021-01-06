<?php

namespace App\Services;

use App\Models\SDE;
use Illuminate\Support\Facades\DB;

class SdeRepository {

    public function searchTypes(string $query) {
        $blueprintGroupIds = $this->_getBlueprintGroupIds();
        return SDE\Inventory\Type::whereNotIn('groupID', $blueprintGroupIds)->where('typeName', 'like', "%{$query}%")->get();
    }

    public function getTypesByIds(array $ids) {
        return SDE\Inventory\Type::whereIn('typeID', $ids)->get();
    }

    public function getTypeById(int $id) {
        return SDE\Inventory\Type::where('typeID', $id)->first();
    }

    private function _getBlueprintGroupIds() {
        return DB::connection('sde')
                 ->table('invGroups')
                 ->select('groupID')
                 ->where('categoryID', 9)
                 ->get()
                 ->map->groupID;
    }
}
