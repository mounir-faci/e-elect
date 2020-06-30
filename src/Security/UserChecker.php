<?php


namespace App\Security;


use App\Entity\User;
use App\Exception\AccountDisabledException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @param UserInterface $user
     * @throws AccountStatusException
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
        // user account is active, the user may be notified
        if (!$user->getActive()) {
            throw new AccountDisabledException($this->translator->trans('exceptions.user_disabled', [], 'translations'));
        }
    }
}
