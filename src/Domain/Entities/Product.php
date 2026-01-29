<?php

namespace App\Domain\Entities;

class Product
{
    public function __construct(
        public ?int $id,
        public int $companyId,
        public string $code,
        public string $name,
        public float $price, // Precio unitario SIN impuestos
        public float $taxRate = 19.00,
        public string $unitMeasureCode = '94', // Unidad
        public bool $isExcluded = false
    ) {
    }

    public function calculateTaxAmount(): float
    {
        if ($this->isExcluded) {
            return 0.0;
        }
        return $this->price * ($this->taxRate / 100);
    }

    public function getPriceWithTax(): float
    {
        return $this->price + $this->calculateTaxAmount();
    }
}
