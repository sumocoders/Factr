<?php
namespace SumoCoders\Factr\Invoice;

/**
 * Class Item
 *
 * @package SumoCoders\Factr\Invoice
 */
class Item
{
    protected string $description;

    protected string $externalProductId;

    protected float $amount;

    protected float $price;

    protected float $totalWithoutVat;

    protected float $totalVat;

    protected float $totalWithVat;

    protected float $totalVatOverrule;

    protected ?int $vat;

    protected int $referenceId;

    protected float $discount;

    protected bool $discountIsPercentage = false;

    protected string $discountDescription;

    protected ?int $productId;

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    private function setReferenceId(int $referenceId): void
    {
        $this->referenceId = $referenceId;
    }

    public function getReferenceId(): int
    {
        return $this->referenceId;
    }

    private function setTotalVat(float $totalVat): void
    {
        $this->totalVat = $totalVat;
    }

    public function getTotalVat(): float
    {
        return $this->totalVat;
    }

    private function setTotalWithVat(float $totalWithVat): void
    {
        $this->totalWithVat = $totalWithVat;
    }

    public function getTotalWithVat(): float
    {
        return $this->totalWithVat;
    }

    private function setTotalWithoutVat(float $totalWithoutVat): void
    {
        $this->totalWithoutVat = $totalWithoutVat;
    }

    public function getTotalWithoutVat(): float
    {
        return $this->totalWithoutVat;
    }

    public function setVat(?int $vat = null): void
    {
        $this->vat = $vat;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function setTotalVatOverrule(float $vatAmount): void
    {
        $this->totalVatOverrule = $vatAmount;
    }

    public function getTotalVatOverrule(): float
    {
        return $this->totalVatOverrule;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): Item
    {
        $this->discount = $discount;

        return $this;
    }

    public function isDiscountAPercentage(): bool
    {
        return $this->discountIsPercentage;
    }

    public function setDiscountIsPercentage(bool $discountIsPercentage): Item
    {
        $this->discountIsPercentage = $discountIsPercentage;

        return $this;
    }

    public function getDiscountDescription(): string
    {
        return $this->discountDescription;
    }

    public function setDiscountDescription(string $discountDescription): Item
    {
        $this->discountDescription = $discountDescription;

        return $this;
    }

    public function getExternalProductId(): string
    {
        return $this->externalProductId;
    }

    public function setExternalProductId(string $externalProductId): void
    {
        $this->externalProductId = $externalProductId;
    }

    public function setProductId(?int $productId): void
    {
        $this->productId = $productId;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * Initialize the object with raw data
     */
    public static function initializeWithRawData(array $data): Item
    {
        $item = new Item();

        if(isset($data['description'])) $item->setDescription($data['description']);
        if(isset($data['amount'])) $item->setAmount($data['amount']);
        if(isset($data['price'])) $item->setPrice($data['price']);
        if(isset($data['vat'])) $item->setVat($data['vat']);
        if(isset($data['reference_id'])) $item->setReferenceId($data['reference_id']);
        if(isset($data['total_without_vat'])) $item->setTotalWithoutVat($data['total_without_vat']);
        if(isset($data['total_vat'])) $item->setTotalVat($data['total_vat']);
        if(isset($data['total_with_vat'])) $item->setTotalWithVat($data['total_with_vat']);
        if(isset($data['discount'])) $item->setDiscount($data['discount']);
        if(isset($data['percentage'])) $item->setDiscountIsPercentage($data['percentage']);
        if(isset($data['discount_description'])) $item->setDiscountDescription($data['discount_description']);
        if(isset($data['external_product_id'])) $item->setExternalProductId($data['external_product_id']);
        if(isset($data['product_id'])) $item->setProductId($data['product_id']);

        return $item;
    }

    /**
     * Converts the object into an array
     */
    public function toArray(bool $forApi = false): array
    {
        $data = array();
        $data['description'] = $this->getDescription();
        $data['price'] = $this->getPrice();
        $data['amount'] = $this->getAmount();
        $data['vat'] = ($forApi && $this->getVat() === null) ? '' : $this->getVat();
        $discount = $this->getDiscount();
        if (!empty($discount)) {
            $data['discount'] = $this->getDiscount();
            $data['percentage'] = $this->isDiscountAPercentage();
            $data['discount_description'] = $this->getDiscountDescription();
        }
        $data['external_product_id'] = $this->getExternalProductId();
        $data['product_id'] = $this->getProductId();

        if ($this->getTotalVatOverrule()) {
            $data['total_vat_overrule'] = $this->getTotalVatOverrule();
        }

        if (!$forApi) {
            $data['total_without_vat'] = $this->getTotalWithoutVat();
            $data['total_vat'] = $this->getTotalVat();
            $data['total_with_vat'] = $this->getTotalWithVat();
            $data['reference_id'] = $this->getReferenceId();
        }

        return $data;
    }
}
