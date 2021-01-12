<?php

namespace App\Services;

use App\Models\SDE\Inventory\Type;
use App\Models\Setting;

class ProductionService {

    private const APPROX_INVENTION_CHANCE = 26;
    private const RUNS_PER_INVENTED_COPY = 10;
    private const USED_DECRYPTOR_ID = 34203;

    private const MANUFACTURING_JOB_COST_MODIFIER = 0.95;
    private const INVENTION_JOB_COST_MODIFIER = 0.74;

    private $_esi;

    private $_decryptor;

    private $_industryIndices;

    public function __construct() {
        $this->_esi = new ESI;
        $this->_decryptor = Type::find(self::USED_DECRYPTOR_ID);

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

        $jobInstallCost = $systemIndex !== null ? $EIV * $systemIndex['cost_index'] * self::MANUFACTURING_JOB_COST_MODIFIER : 0;

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

        $jobInstallCost = $systemIndex !== null ? $EIV * $systemIndex['cost_index'] * self::INVENTION_JOB_COST_MODIFIER : 0;

        return ceil(($cost + $this->_decryptor->jitaPrice + $jobInstallCost) / (self::APPROX_INVENTION_CHANCE / 100) / self::RUNS_PER_INVENTED_COPY);
    }
}
