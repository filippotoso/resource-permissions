<?php

namespace FilippoToso\ResourcePermissions\Data;

use Illuminate\Database\Eloquent\Model;

class ResourceData
{
    public function __construct(public string $type, public int $id) {}

    public static function resources($resources): array
    {
        $resources = is_iterable($resources) ? $resources : [$resources];

        $results = [];

        // [Class => id, Class => id, etc.]
        // [Model, Model, etc.]
        // [ResourceData, ResourceData, etc.]

        foreach ($resources as $class => $value) {
            if (is_string($class) && is_numeric($value)) {
                $type = static::morphClass($class);
                $results[] = new self($type, $value);
            }

            if (is_a($value, Model::class, true)) {
                $results[] = new self($value->getMorphClass(), $value->getKey());
            }

            if (is_a($value, ResourceData::class, true)) {
                $results[] = $value;
            }
        }

        return $results;
    }

    protected static function morphClass($class)
    {
        if (is_a($class, Model::class, true)) {
            return (new $class)->getMorphClass();
        }

        return $class;
    }
}
