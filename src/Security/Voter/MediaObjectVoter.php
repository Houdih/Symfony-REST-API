<?php

namespace App\Security\Voter;

use App\Entity\MediaObject;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class MediaObjectVoter extends Voter
{
    const CREATE = 'MEDIA_CREATE';
    const EDIT = 'MEDIA_EDIT';
    const DELETE = 'MEDIA_DELETE';

    private $security;

    public function __construct(Security $security) {    
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {        
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof MediaObject;
    }

    /**
     * @param $attribute
     * @param MediaObject $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin and Author all granted
        if($this->security->isGranted('ROLE_ADMIN', 'ROLE_AUTHOR')) {
            return true;
        }

    }
}