<?php

namespace Tests\unit\Statistics\Calculator;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\AveragePostsNumberPerUser;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;
use Traversable;

class AveragePostsNumberPerUserTest extends TestCase
{
    private const STAT_NAME = 'mock stat name';

    public function testsEmptyData()
    {
        $calculator = $this->createConfiguredCalculator();
        $res        = $calculator->calculate();

        $expectedRes = new StatisticsTo();
        $expectedRes->setUnits('posts');
        $expectedRes->setValue(0);
        $expectedRes->setName(self::STAT_NAME);

        $this->assertEquals($expectedRes, $res);
    }

    public function testNonEmptyData()
    {
        $calculator = $this->createConfiguredCalculator();
        $posts      = $this->generateMockPosts();

        foreach ($posts as $post) {
            $calculator->accumulateData($post);
        }

        $res = $calculator->calculate();

        $expectedRes = new StatisticsTo();
        $expectedRes->setUnits('posts');
        $expectedRes->setValue(3.0);
        $expectedRes->setName(self::STAT_NAME);

        $this->assertEquals($expectedRes, $res);
    }

    private function createConfiguredCalculator(): AveragePostsNumberPerUser
    {
        $calculator = new AveragePostsNumberPerUser();

        $parameters = new ParamsTO();

        $format    = 'Y-m-d H:i:s';
        $tz = new DateTimeZone('UTC');

        $startDate = DateTime::createFromFormat($format, '2022-03-13 00:00:00', $tz);
        $endDate   = DateTime::createFromFormat($format, '2022-03-13 23:59:59', $tz);

        $parameters->setStartDate($startDate);
        $parameters->setEndDate($endDate);
        $parameters->setStatName(self::STAT_NAME);
        $calculator->setParameters($parameters);

        return $calculator;
    }

    /**
     * @return Traversable<SocialPostTo>
     */
    private function generateMockPosts(): Traversable
    {
        $format    = 'Y-m-d H:i:s';
        $tz = new DateTimeZone('UTC');

        foreach ($this->getMockPostData() as $datum) {
            $post = new SocialPostTo();
            $post->setAuthorId($datum[1]);
            $post->setDate(DateTime::createFromFormat($format, $datum[0], $tz));

            yield $post;
        }
    }

    private function getMockPostData(): array
    {
        return [
            [
                '2022-03-12 13:14:15',
                "user_1",
            ],
            [
                '2022-03-13 00:00:00',  # will be considered
                "user_1"
            ],
            [
                '2022-03-13 01:00:00', # will be considered
                "user_1"
            ],
            [
                '2022-03-13 01:00:00',
                null
            ],
            [
                '2022-03-13 23:59:59', # will be considered
                "user_1"
            ],
            [
                '2022-03-14 00:00:00',
                "user_1"
            ],
        ];
    }

}