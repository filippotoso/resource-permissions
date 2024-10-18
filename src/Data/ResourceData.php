<?php

namespace FilippoToso\ResourcePermissions\Data;

use Illuminate\Database\Eloquent\Model;

class ResourceData
{
    public function __construct(public string $type, public int $id) {}

    public static function resources($resources): array
    {
        $resources = is_iterable($resources) ? $resources : [$resources];

        // [Class => id]
        if (count($resources) == 1) {
            reset($resources);

            $key = key($resources);
            $value = current($resources);

            if (is_string($key) && is_numeric($value)) {
                $type = static::morphClass($key);

                return [new self($type, $value)];
            }

            if (is_a($value, Model::class, true)) {
                return [new self($value->getMorphClass(), $value->getKey())];
            }
        }

        // [Class, id]
        if (count($resources) == 2 && (is_string($resources[0] ?? null) && (is_numeric($resources[1] ?? null)))) {
            $type = static::morphClass($resources[0]);

            return [new self($type, $resources[1])];
        }

        $results = [];

        // [Class => id, Class => id, etc.]
        // [Model, Model, etc.]

        foreach ($resources as $key => $value) {
            if (is_string($key) && is_numeric($value)) {
                $type = static::morphClass($key);
                $results[] = new self($type, $value);
            }

            if (is_a($value, Model::class, true)) {
                $results[] = new self($value->getMorphClass(), $value->getKey());
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
