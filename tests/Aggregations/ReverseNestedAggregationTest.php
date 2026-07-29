<?php

namespace Spatie\ElasticsearchQueryBuilder\Tests\Aggregations;

use PHPUnit\Framework\TestCase;
use Spatie\ElasticsearchQueryBuilder\Aggregations\ReverseNestedAggregation;
use Spatie\ElasticsearchQueryBuilder\Aggregations\TermsAggregation;
use stdClass;

class ReverseNestedAggregationTest extends TestCase
{
    public function testCreateReturnsNewInstance(): void
    {
        $aggregation = ReverseNestedAggregation::create('test_name');

        self::assertInstanceOf(ReverseNestedAggregation::class, $aggregation);
    }

    public function testDefaultSetupOmitsAggsKeyWhenThereAreNoSubAggregations(): void
    {
        $aggregation = new ReverseNestedAggregation('test_name');

        $payload = $aggregation->toArray();

        self::assertArrayNotHasKey('aggs', $payload);
        self::assertEquals([
            'reverse_nested' => new stdClass(),
        ], $payload);
    }

    public function testDefaultSetupWithStaticCreateFunctionOmitsAggsKey(): void
    {
        $aggregation = ReverseNestedAggregation::create('test_name');

        $payload = $aggregation->toArray();

        self::assertArrayNotHasKey('aggs', $payload);
        self::assertEquals([
            'reverse_nested' => new stdClass(),
        ], $payload);
    }

    public function testWithSubAggregation(): void
    {
        $aggregation = (new ReverseNestedAggregation('test_name'))
            ->aggregation(new TermsAggregation('test_agg_name_1', 'test_agg_field_1'));

        self::assertEquals([
            'reverse_nested' => new stdClass(),
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
        $aggregation = new ReverseNestedAggregation(
            'test_name',
            new TermsAggregation('test_agg_name_1', 'test_agg_field_1'),
            new TermsAggregation('test_agg_name_2', 'test_agg_field_2')
        );

        self::assertEquals([
            'reverse_nested' => new stdClass(),
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
        $aggregation = ReverseNestedAggregation::create(
            'test_name',
            TermsAggregation::create('test_agg_name_1', 'test_agg_field_1'),
            TermsAggregation::create('test_agg_name_2', 'test_agg_field_2')
        );

        self::assertEquals([
            'reverse_nested' => new stdClass(),
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

    public function testEncodingProducesJsonObjectNotArrayWhenThereAreNoSubAggregations(): void
    {
        $aggregation = ReverseNestedAggregation::create('test_name');

        $json = json_encode($aggregation->toArray());

        self::assertStringNotContainsString('"aggs":[]', $json);
    }
}
