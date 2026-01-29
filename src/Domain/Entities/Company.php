<?php

namespace App\Domain\Entities;

class Company
{
    public function __construct(
        public ?int $id,
        public string $nit,
        public string $dv,
        public string $companyName,
        public string $email,
        public string $address,
        public string $cityCode,
        public string $departmentCode,
        public string $taxLevelCode = 'O-13',
        public string $regimeCode = '48',
        public ?string $certificatePath = null,
        public ?string $certificatePassword = null,
        public ?string $softwareId = null,
        public ?string $pin = null,
        public ?string $testSetId = null,
        public string $environment = 'TEST'
    ) {
    }

    public function getFullName(): string
    {
        return $this->companyName;
    }

    public function getCompleteNit(): string
    {
        return $this->nit . '-' . $this->dv;
    }

    public function isProduction(): bool
    {
        return $this->environment === 'PROD';
    }
}
