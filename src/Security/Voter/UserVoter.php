<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof User;
    }

    /**
     * @param $attribute
     * @param User $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Le rôle 'admin' a touts les accès
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Only user can edit or delete his profil
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
               if($user === $subject->getUserIdentifier()) {
                return true;
                break;
               }
           
        }

        return false;
    }
}
