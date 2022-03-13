<?php

namespace Tests\unit\Statistics\Calculator\Factory;

use PHPUnit\Framework\TestCase;
use Statistics\Calculator\Factory\StatisticsCalculatorFactory;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;

class StatisticsCalculatorFactoryTest extends TestCase
{
    public function testProduceAveragePostsNumberPerUserTest()
    {
        $params = new ParamsTo();
        $params->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER);
        $calculatorComposite = StatisticsCalculatorFactory::create([$params]);

        // CalculatorComposite does not allow us to directly inspect its children,
        // so instead we will run a calculation and inspect the children of the resulting
        // composite statistics to prove that our new calculator has added an
        // empty statistics there.
        // Alternatively, we could have added a "getChildren" method to CalculatorComposite
        $res                 = $calculatorComposite->calculate();
        $this->assertCount(2, $res->getChildren());
        $this->assertEquals(StatsEnum::AVERAGE_POST_NUMBER_PER_USER, $res->getChildren()[0]->getName());
    }
}