<?php

namespace App\Security\Voter;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em){
        $this->security = $security;
        $this->em = $em;
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

        if (!$user instanceof UserInterface || !$user instanceof User) {
            return false;
        }
        $userId = $user->getId();
        $userRepo = $this->em->getRepository(User::class);
        $user = $user = $userRepo->find($userId);

        // Le rôle 'admin' a touts les accès
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Only user can edit or delete his profil
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
               if($user === $subject) {
                return true;
                break;
               }
           
        }

        return false;
    }
}
