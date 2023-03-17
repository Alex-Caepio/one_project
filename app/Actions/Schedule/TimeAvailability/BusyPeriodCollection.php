<?php

namespace App\Actions\Schedule\TimeAvailability;

use ArrayIterator;
use Carbon\Carbon;
use IteratorAggregate;
use Traversable;
use TypeError;

/**
 * @method BusyPeriod[] getIterator()
 */
class BusyPeriodCollection implements IteratorAggregate
{
    /**
     * @var BusyPeriod[]
     */
    private array $items = [];

    /**
     * @param BusyPeriod[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_filter($items, fn ($item): bool => $this->throwIfInvalid($item));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function push(BusyPeriod $item): self
    {
        $this->throwIfInvalid($item);

        $this->items[] = $item;

        return $this;
    }

    public function merge(BusyPeriodCollection $collection): self
    {
        return new self(array_merge($this->items, $collection->getItems()));
    }

    /**
     * Creates and pushes a new busy period by the given times.
     */
    public function addFromTimes(Carbon $from, Carbon $to): self
    {
        return $this->push(new BusyPeriod($from, $to));
    }

    /**
     * @return BusyPeriod[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    private function throwIfInvalid($item): bool
    {
        return !throw_if(!$item instanceof BusyPeriod, TypeError::class);
    }
}
