<?php


namespace App\Services;


use App\Models\SDE\Inventory\Type;

class ProductionService {

    private $_esi;

    private $_decryptor;

    public function __construct() {
        $this->_esi = new ESI;
        $this->_decryptor = Type::find(34203);
    }

    public function getTypeProductionCost($type) {
        $cost = 0;
        foreach ($type->blueprintProductionMaterials as $material) {
            $cost += $material->materialType->jitaPrice * $material->quantity;
        }

        return ceil($cost);
    }

    public function getTypeInventionCost($type) {
        $cost = 0;
        foreach ($type->blueprintInventionMaterials as $material) {
            $cost += $material->materialType->jitaPrice * $material->quantity;
        }

        return ceil(($cost + $this->_decryptor->jitaPrice) * 4 / 10);
    }

    private function _getDichstarSystemCostIndices() {
        $industrySystems = $this->_esi->getIndustrySystems()->getArrayCopy();
        return collect($industrySystems)
            ->first(function ($system) {
                return $system->solar_system_id === 30000843;
            })
            ->cost_indices;
    }
}
