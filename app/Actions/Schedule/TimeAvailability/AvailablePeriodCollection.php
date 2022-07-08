<?php

namespace App\Actions\Schedule\TimeAvailability;

use ArrayIterator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use IteratorAggregate;
use Traversable;

/**
 * @method CarbonPeriod[] getIterator()
 */
class AvailablePeriodCollection implements IteratorAggregate
{
    /**
     * @var CarbonPeriod[]
     */
    private array $items = [];

    /**
     * @param CarbonPeriod[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_filter($items, fn ($item): bool => $this->throwIfInvalid($item));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function push(CarbonPeriod $item): self
    {
        $this->throwIfInvalid($item);

        $this->items[] = $item;

        return $this;
    }

    /**
     * Excludes the available periods by the given busy periods and return
     * a new collection.
     */
    public function excludeBusyPeriods(BusyPeriodCollection $busyPeriods): self
    {
        $items = [];

        foreach ($this->items as $originalPeriod) {
            $period = $originalPeriod->clone();

            foreach ($busyPeriods as $busyPeriod) {
                $period->filter(fn (Carbon $date): bool => !$busyPeriod->doesIncludeTime($date));
            }

            if ($period->count()) {
                $items[] = $period;
            }
        }

        return new self($items);
    }

    /**
     * Converts periods to times with the timezone.
     *
     * @return string[]
     */
    public function toTimes(string $timezone = 'UTC', string $format = 'H:i'): array
    {
        $times = [];

        foreach ($this->items as $period) {
            foreach ($period as $time) {
                $times[] = $time->setTimezone($timezone)->format($format);
            }
        }

        sort($times, SORT_NATURAL);

        return array_unique($times);
    }

    private function throwIfInvalid($item): bool
    {
        return !throw_if(!$item instanceof CarbonPeriod, TypeError::class);
    }
}
