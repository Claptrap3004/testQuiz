<?php

namespace quiz;

class UserStats
{
    private int $userId;
    private int $timesAsked = 0;
    private int $timesRight = 0;
    private float $rate;

    public function __construct(User $user)
    {
        $this->userId = $user->getId();
        $this->setStats();
    }

    private function setStats(): void
    {
        $allStats = KindOf::STATS->getDBHandler()->findAll(['userId' => $this->userId]);
        foreach ($allStats as $stat){
            $this->timesAsked += $stat['times_asked'];
            $this->timesRight += $stat['times_right'];
        }
        $this->setRate();
    }

    private function setRate(): void
    {
        $percentage = $this->timesAsked != 0 ? $this->timesRight * 100 / $this->timesAsked : 0;
        $this->rate = round($percentage,2);
    }

    public function getTimesAsked(): int
    {
        return $this->timesAsked;
    }

    public function getTimesRight(): int
    {
        return $this->timesRight;
    }

    public function getRate(): float
    {
        return $this->rate;
    }


}