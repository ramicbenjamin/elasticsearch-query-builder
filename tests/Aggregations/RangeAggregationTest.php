<?php

namespace Spatie\ElasticsearchQueryBuilder\Tests\Aggregations;

use PHPUnit\Framework\TestCase;
use Spatie\ElasticsearchQueryBuilder\Aggregations\RangeAggregation;
use Spatie\ElasticsearchQueryBuilder\Aggregations\TermsAggregation;

class RangeAggregationTest extends TestCase
{
    public function testCreateReturnsNewInstance(): void
    {
        $aggregation = RangeAggregation::create('price_ranges', 'price', [
            ['to' => 100.0],
        ]);

        self::assertInstanceOf(RangeAggregation::class, $aggregation);
    }

    public function testDefaultSetup(): void
    {
        $aggregation = new RangeAggregation('price_ranges', 'price', [
            ['to' => 100.0],
            ['from' => 100.0, 'to' => 200.0],
            ['from' => 200.0],
        ]);

        self::assertEquals([
            'range' => [
                'field' => 'price',
                'ranges' => [
                    ['to' => 100.0],
                    ['from' => 100.0, 'to' => 200.0],
                    ['from' => 200.0],
                ],
            ],
        ], $aggregation->toArray());
    }

    public function testDefaultSetupWithStaticCreateFunction(): void
    {
        $aggregation = RangeAggregation::create('price_ranges', 'price', [
            ['to' => 100.0],
            ['from' => 100.0],
        ]);

        self::assertEquals([
            'range' => [
                'field' => 'price',
                'ranges' => [
                    ['to' => 100.0],
                    ['from' => 100.0],
                ],
            ],
        ], $aggregation->toArray());
    }

    public function testWithCustomRangeKey(): void
    {
        $aggregation = RangeAggregation::create('price_ranges', 'price', [
            ['key' => 'cheap', 'to' => 100.0],
            ['key' => 'expensive', 'from' => 100.0],
        ]);

        self::assertEquals([
            'range' => [
                'field' => 'price',
                'ranges' => [
                    ['key' => 'cheap', 'to' => 100.0],
                    ['key' => 'expensive', 'from' => 100.0],
                ],
            ],
        ], $aggregation->toArray());
    }

    public function testWithoutSubAggregationsOmitsAggsKey(): void
    {
        $aggregation = new RangeAggregation('price_ranges', 'price', [
            ['to' => 100.0],
        ]);

        $payload = $aggregation->toArray();

        self::assertArrayNotHasKey('aggs', $payload);
    }

    public function testWithSubAggregation(): void
    {
        $aggregation = (new RangeAggregation('price_ranges', 'price', [
            ['to' => 100.0],
        ]))->aggregation(new TermsAggregation('test_agg_name_1', 'test_agg_field_1'));

        self::assertEquals([
            'range' => [
                'field' => 'price',
                'ranges' => [
                    ['to' => 100.0],
                ],
            ],
            'aggs' => [
                'test_agg_name_1' => [
                    'terms' => [
                        'field' => 'test_agg_field_1',
                    ],
                ],
            ],
        ], $aggregation->toArray());
    }

    public function testFullSetupWithMultipleSubAggregations(): void
    {
        $aggregation = new RangeAggregation(
            'price_ranges',
            'price',
            [['to' => 100.0], ['from' => 100.0]],
            new TermsAggregation('test_agg_name_1', 'test_agg_field_1'),
            new TermsAggregation('test_agg_name_2', 'test_agg_field_2')
        );

        self::assertEquals([
            'range' => [
                'field' => 'price',
                'ranges' => [
                    ['to' => 100.0],
                    ['from' => 100.0],
                ],
            ],
            'aggs' => [
                'test_agg_name_1' => [
                    'terms' => [
                        'field' => 'test_agg_field_1',
                    ],
                ],
                'test_agg_name_2' => [
                    'terms' => [
                        'field' => 'test_agg_field_2',
                    ],
                ],
            ],
        ], $aggregation->toArray());
    }

    public function testFullSetupWithStaticCreateFunction(): void
    {
        $aggregation = RangeAggregation::create(
            'price_ranges',
            'price',
            [['to' => 100.0], ['from' => 100.0]],
            TermsAggregation::create('test_agg_name_1', 'test_agg_field_1'),
            TermsAggregation::create('test_agg_name_2', 'test_agg_field_2')
        );

        self::assertEquals([
            'range' => [
                'field' => 'price',
                'ranges' => [
                    ['to' => 100.0],
                    ['from' => 100.0],
                ],
            ],
            'aggs' => [
                'test_agg_name_1' => [
                    'terms' => [
                        'field' => 'test_agg_field_1',
                    ],
                ],
                'test_agg_name_2' => [
                    'terms' => [
                        'field' => 'test_agg_field_2',
                    ],
                ],
            ],
        ], $aggregation->toArray());
    }
}
