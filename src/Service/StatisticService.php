<?php


namespace App\Service;


use App\Entity\Election;
use App\Entity\User;
use App\Model\DefaultStatistics;
use App\Model\Statistic;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatisticService
{
    private $translator;
    private $userService;
    private $applicationService;
    private $electionService;
    private $voteService;

    public function __construct(TranslatorInterface $translator, UserService $userService, ApplicationService $applicationService, ElectionService $electionService, VoteService $voteService)
    {
        $this->translator = $translator;
        $this->userService = $userService;
        $this->applicationService = $applicationService;
        $this->electionService = $electionService;
        $this->voteService = $voteService;
    }

    public function getStatistics(User $user): DefaultStatistics
    {
        $defaultStatistics = new DefaultStatistics();
        $this->getUserStatistics($user, $defaultStatistics);

        if ($user->isAdministrator()) {
            $this->getAdminStatistics($user, $defaultStatistics);
        }
        return $defaultStatistics;
    }

    private function translate(string $key): string
    {
        return $this->translator->trans($key, [], 'translations');
    }

    private function getUserStatistics(User $user, DefaultStatistics $defaultStatistics)
    {
        $defaultStatistics
            ->addUserStatistic(
                (new Statistic())
                    ->setIcon('medal')
                    ->setColor('primary')
                    ->setLabel($this->translate('back.default.pages.statistics.specific.elections'))
                    ->setValue(count($this->electionService->getElections(Election::STATUS_PENDING)))
            )
            ->addUserStatistic(
                (new Statistic())
                    ->setIcon('layer-group')
                    ->setColor('primary')
                    ->setLabel($this->translate('back.default.pages.statistics.specific.applications'))
                    ->setValue($user->getApplications()->count())
            )
            ->addUserStatistic(
                (new Statistic())
                    ->setIcon('crown')
                    ->setColor('primary')
                    ->setLabel($this->translate('back.default.pages.statistics.specific.votes.sent'))
                    ->setValue($user->getVotes()->count())
            )
            ->addUserStatistic(
                (new Statistic())
                    ->setIcon('crown')
                    ->setColor('primary')
                    ->setLabel($this->translate('back.default.pages.statistics.specific.votes.received'))
                    ->setValue(count($this->voteService->getUserReceivedVotes($user)))
            );
    }

    private function getAdminStatistics(User $user, DefaultStatistics $defaultStatistics)
    {
        $defaultStatistics
            ->addAdminStatistic(
                (new Statistic())
                    ->setIcon('medal')
                    ->setColor('success')
                    ->setLabel($this->translate('back.default.pages.statistics.global.elections'))
                    ->setValue(count($this->electionService->getElections()))
            )
            ->addAdminStatistic(
                (new Statistic())
                    ->setIcon('layer-group')
                    ->setColor('success')
                    ->setLabel($this->translate('back.default.pages.statistics.global.applications'))
                    ->setValue(count($this->applicationService->getApplications()))
            )
            ->addAdminStatistic(
                (new Statistic())
                    ->setIcon('users')
                    ->setColor('success')
                    ->setLabel($this->translate('back.default.pages.statistics.global.users'))
                    ->setValue(count($this->userService->getUsers()))
            )
            ->addAdminStatistic(
                (new Statistic())
                    ->setIcon('crown')
                    ->setColor('success')
                    ->setLabel($this->translate('back.default.pages.statistics.global.votes'))
                    ->setValue(count($this->voteService->getVotes()))
            );
    }
}
