<?php

namespace Spatie\ElasticsearchQueryBuilder\Aggregations;

use Spatie\ElasticsearchQueryBuilder\AggregationCollection;
use Spatie\ElasticsearchQueryBuilder\Aggregations\Concerns\WithAggregations;

class RangeAggregation extends Aggregation
{
    use WithAggregations;

    protected string $field;

    protected array $ranges;

    public static function create(
        string $name,
        string $field,
        array $ranges,
        Aggregation ...$aggregations
    ): self {
        return new self($name, $field, $ranges, ...$aggregations);
    }

    public function __construct(
        string $name,
        string $field,
        array $ranges,
        Aggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->field = $field;
        $this->ranges = $ranges;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function payload(): array
    {
        $aggregation = [
            'range' => [
                'field' => $this->field,
                'ranges' => $this->ranges,
            ],
        ];

        if (! $this->aggregations->isEmpty()) {
            $aggregation['aggs'] = $this->aggregations->toArray();
        }

        return $aggregation;
    }
}
