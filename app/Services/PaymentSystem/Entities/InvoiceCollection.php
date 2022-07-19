<?php

namespace App\Services\PaymentSystem\Entities;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class InvoiceCollection implements IteratorAggregate, Countable
{
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return Invoice[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Invoice[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function removeEmpty(): self
    {
        $items = array_filter($this->items, static function (Invoice $item): bool {
            return $item->amountPaid > 0;
        });

        return new self($items);
    }
}
