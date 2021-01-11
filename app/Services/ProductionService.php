<?php


namespace App\Services;


use App\Models\SDE\Inventory\Type;
use App\Models\Setting;

class ProductionService {

    private $_esi;

    private $_decryptor;

    private $_industryIndices;

    public function __construct() {
        $this->_esi = new ESI;
        $this->_decryptor = Type::find(34203);

        $industryIndicesSetting = Setting::getData('industry_indices');
        $industryIndicesJson = $industryIndicesSetting !== null ? $industryIndicesSetting->value : null;
        $this->_industryIndices = collect($industryIndicesJson !== null ? json_decode($industryIndicesJson, true) : []);
    }

    public function getTypeProductionCost($type) {
        $cost = 0;
        $EIV = 0;
        foreach ($type->blueprintProductionMaterials as $material) {
            $cost += $material->materialType->jitaPrice * $material->quantity;
            $EIV += $material->materialType->adjustedPrice * $material->quantity;
        }

        $systemIndex = $this->_industryIndices->first(function ($index) {
            return $index['activity'] === 'manufacturing';
        });

        $jobInstallCost = $systemIndex !== null ? $EIV * $systemIndex['cost_index'] * 0.95 : 0;

        return ceil($cost + $jobInstallCost);
    }

    public function getTypeInventionCost($type) {
        $cost = 0;
        $EIV = 0;
        foreach ($type->blueprintInventionMaterials as $material) {
            $cost += $material->materialType->jitaPrice * $material->quantity;
            $EIV += $material->materialType->adjustedPrice * $material->quantity;
        }

        $systemIndex = $this->_industryIndices->first(function ($index) {
            return $index['activity'] === 'invention';
        });

        $jobInstallCost = $systemIndex !== null ? $EIV * $systemIndex['cost_index'] * 0.74 : 0;

        return ceil(($cost + $this->_decryptor->jitaPrice + $jobInstallCost) * 4 / 10);
    }
}
