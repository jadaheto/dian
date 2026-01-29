<?php

namespace App\Domain\Entities;

class InvoiceItem
{
    public function __construct(
        public ?int $id, // ID en base de datos (si existe)
        public string $productCode,
        public string $productName,
        public float $quantity,
        public float $unitPrice,
        public float $taxRate
    ) {
    }

    public function getSubtotal(): float
    {
        return $this->quantity * $this->unitPrice;
    }

    public function getTaxAmount(): float
    {
        return $this->getSubtotal() * ($this->taxRate / 100);
    }

    public function getTotal(): float
    {
        return $this->getSubtotal() + $this->getTaxAmount();
    }
}

class Invoice
{
    /** @var InvoiceItem[] */
    private array $items = [];

    public function __construct(
        public ?int $id,
        public int $companyId,
        public int $customerId,
        public int $resolutionId,
        public string $prefix,
        public int $number,
        public \DateTime $issueDate,
        public string $paymentForm = '1', // 1=Contado
        public string $dianStatus = 'DRAFT'
    ) {
    }

    public function addItem(InvoiceItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function calculateTotals(): array
    {
        $subtotal = 0.0;
        $taxAmount = 0.0;
        $total = 0.0;

        foreach ($this->items as $item) {
            $subtotal += $item->getSubtotal();
            $taxAmount += $item->getTaxAmount();
        }

        $total = $subtotal + $taxAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2)
        ];
    }

    public function getFormattedDate(): string
    {
        return $this->issueDate->format('Y-m-d');
    }

    public function getFormattedTime(): string
    {
        return $this->issueDate->format('H:i:s');
    }
}
