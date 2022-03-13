<?php


namespace Statistics\Calculator;


use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AveragePostsNumberPerUser extends AbstractCalculator
{

    protected const UNITS = 'posts';


    private int $postCount = 0;

    /**
     * @var array<string, bool>
     *
     * contains a map of unique user_id values to true
     */
    private array $usersMap = [];

    protected function checkPost(SocialPostTo $postTo): bool
    {
        // for the purpose of this calculator, we ignore posts not attributed to
        // any user
        if ($postTo->getAuthorId() === null) {
            return false;
        }
        return parent::checkPost($postTo);
    }


    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $this->postCount++;
        $this->usersMap[$postTo->getAuthorId()] = true;
    }

    protected function doCalculate(): StatisticsTo
    {
        $uniqueUsersCount = count($this->usersMap);

        $value = $this->postCount > 0
            ? $this->postCount / $uniqueUsersCount
            : 0;

        return (new StatisticsTo())->setValue(round($value,2));
    }
}