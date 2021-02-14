<?php

namespace App\Services\Locations;

class Location {

    private $_id;

    private $_name;

    private $_regionId;

    private $_isStructure;

    private $_isTradingHub;

    private $_deliveryCost;

    public function __construct(int $id, string $name, int $regionId, bool $isStructure, bool $isTradingHub, ?int $deliveryCost) {
        $this->_id = $id;
        $this->_name = $name;
        $this->_regionId = $regionId;
        $this->_isStructure = $isStructure;
        $this->_isTradingHub = $isTradingHub;
        $this->_deliveryCost = $deliveryCost;
    }

    public function id(): int {
        return $this->_id;
    }

    public function name(): string {
        return $this->_name;
    }

    public function regionId(): int {
        return $this->_regionId;
    }

    public function isStructure(): bool {
        return $this->_isStructure;
    }

    public function isTradingHub(): bool {
        return $this->_isTradingHub;
    }

    public function deliveryCost(): ?int {
        return $this->_deliveryCost;
    }
}
