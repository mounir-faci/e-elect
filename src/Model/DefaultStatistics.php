<?php


namespace App\Model;


class DefaultStatistics
{
    /** @var Statistic[] */
    private $userStatistics;

    /** @var Statistic[] */
    private $adminStatistics;

    public function getUserStatistics(): array
    {
        return $this->userStatistics;
    }

    public function setUserStatistics(array $userStatistics): self
    {
        $this->userStatistics = $userStatistics;
        return $this;
    }

    public function addUserStatistic(Statistic $statistic): self
    {
        $this->userStatistics[] = $statistic;
        return $this;
    }

    public function getAdminStatistics(): array
    {
        return $this->adminStatistics;
    }

    public function setAdminStatistics(array $adminStatistics): self
    {
        $this->adminStatistics = $adminStatistics;
        return $this;
    }

    public function addAdminStatistic(Statistic $statistic): self
    {
        $this->adminStatistics[] = $statistic;
        return $this;
    }
}
