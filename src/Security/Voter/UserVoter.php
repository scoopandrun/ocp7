<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const LIST = 'USER_LIST';
    public const VIEW = 'USER_VIEW';
    public const CREATE = 'USER_CREATE';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        if (in_array($attribute, [self::LIST, self::CREATE])) {
            return true;
        }

        if (in_array($attribute, [self::VIEW, self::DELETE]) && $subject instanceof \App\Entity\User) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if (!is_null($subject) && !$subject instanceof User) {
            throw new \LogicException('The subject must be an instance of User');
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::LIST:
                return $this->canList($user);
            case self::VIEW:
                return $this->canView($user, $subject);
            case self::CREATE:
                return $this->canCreate($user);
            case self::DELETE:
                return $this->canDelete($user, $subject);
        }

        throw new \LogicException('This case is not implemented: ' . $attribute);
    }

    private function canList(User $user): bool
    {
        // A user can always view other users
        return true;
    }

    private function canView(User $user, User $subject): bool
    {
        // A user can view another user if they are part of the same company
        return $user->getCompany() === $subject->getCompany();
    }

    private function canCreate(User $user): bool
    {
        // A user can always create another user
        return true;
    }

    private function canDelete(User $user, User $subject): bool
    {
        // A user can delete another user if they are part of the same company
        return $user->getCompany() === $subject->getCompany();
    }
}
