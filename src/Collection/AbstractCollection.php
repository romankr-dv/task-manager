<?php

namespace App\Collection;

use Countable;
use Iterator;
use ArrayIterator;
use IteratorAggregate;

abstract class AbstractCollection implements IteratorAggregate, Countable
{
    public function __construct(protected array $list) {}

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->list);
    }

    public function toArray(): array
    {
        return $this->list;
    }

    public function isEmpty(): bool
    {
        return empty($this->list);
    }

    public function count(): int
    {
        return count($this->list);
    }
}
