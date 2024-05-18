<?php

namespace App\Security\Voter;

use App\Entity\Device;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeviceVoter extends Voter
{
    public const VIEW = 'DEVICE_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        if (
            in_array($attribute, [self::VIEW])
        ) {
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

        if (!is_null($subject) && !$subject instanceof Device) {
            throw new \LogicException('The subject must be an instance of Device');
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user);
        }

        throw new \LogicException('This case is not implemented: ' . $attribute);
    }

    private function canView(User $user): bool
    {
        // A user can view devices if the attached customer has the right to use the API
        return $user->getCustomer()->canUseApi();
    }
}
