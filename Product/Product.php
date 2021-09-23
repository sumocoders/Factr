<?php

namespace SumoCoders\Factr\Product;

final class Product
{
    private ?int $id;

    private string $name;

    private string $description;

    private float $price;

    private int $vat;

    public function __construct(
        string $name,
        string $description,
        float $price,
        int $vat,
        ?int $id = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->vat = $vat;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function setVat(int $vat): void
    {
        $this->vat = $vat;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public static function initializeWithRawData(array $data): Product
    {
        return new self(
            $data['name'],
            $data['description'],
            $data['price'],
            $data['vat'],
            $data['id']
        );
    }

    public function toArray(): array
    {
        $data = array();

        $data['id'] = $this->getId();
        $data['name'] = $this->getName();
        $data['description'] = $this->getDescription();
        $data['price'] = $this->getPrice();
        $data['vat'] = $this->getVat();

        return $data;
    }
}
