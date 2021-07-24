<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class BaseDTOCollection extends DataTransferObjectCollection
{
    public function current()
    {
        return parent::current();
    }

    public static function create(array $data, string $class)
    {
        $collection = [];

        foreach ($data as $item) {
            $collection[] = $class::create($item);
        }

        return collect($collection);
    }
}
