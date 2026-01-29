<?php

namespace App\Domain\Entities;

class Customer
{
    public function __construct(
        public ?int $id,
        public int $companyId,
        public string $identificationType, // 13: Cédula, 31: NIT
        public string $identificationNumber,
        public ?string $dv,
        public string $name,
        public string $email,
        public ?string $address = null,
        public ?string $phone = null,
        public ?string $cityCode = null,
        public ?string $departmentCode = null,
        public string $taxLevelCode = 'R-99-PN'
    ) {
    }

    public function isJuridical(): bool
    {
        return $this->identificationType === '31'; // NIT
    }

    public function getCompleteId(): string
    {
        return $this->identificationNumber . ($this->dv ? '-' . $this->dv : '');
    }
}
