<?php

namespace App\Services;

use App\Models\SDE;
use Illuminate\Support\Facades\DB;

class SdeRepository {

    public function searchTypes(string $query) {
        $blueprintGroupIds = $this->_getBlueprintGroupIds();
        return SDE\Inventory\Type::whereNotNull('marketGroupID')
                                 ->where('published', 1)
                                 ->whereNotIn('groupID', $blueprintGroupIds)
                                 ->where('typeName', 'like', "%{$query}%")
                                 ->get();
    }

    public function searchRigs(string $query) {
        return SDE\Inventory\Type::rigs()
            ->where('typeName', 'like', "%{$query}%")
            ->with([
                'techLevelAttribute',
                'blueprint.productionMaterials',
                'blueprint.tech1Blueprint.inventionMaterials',
            ])
            ->get();
    }

    public function getTypesByIds(array $ids, $with = []) {
        $query = SDE\Inventory\Type::whereIn('typeID', $ids);
        if (count($with) > 0) {
            $query = $query->with($with);
        }

        return $query->get();
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
