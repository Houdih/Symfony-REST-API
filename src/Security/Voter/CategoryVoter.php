<?php

namespace App\Security\Voter;

use App\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * author and admin can create, put or delete a category
 * but i leave the future possibility to modify the accesses
 */
class CategoryVoter extends Voter
{
    const CREATE = 'CATEGORY_CREATE';
    const EDIT = 'CATEGORY_EDIT';
    const DELETE = 'CATEGORY_DELETE';

    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {        
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Category;
    }

    /**
     * @param string $attribute
     * @param Category $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin and Author all granted
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
    }
}