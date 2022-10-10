<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class CommentVoter extends Voter
{
    const CREATE = 'COMMENT_CREATE';
    const EDIT = 'COMMENT_EDIT';
    const DELETE = 'COMMENT_DELETE';

    private $security;

    public function __construct(Security $security) {    
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {        
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    /**
     * @param $attribute
     * @param Comment $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin all granted
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // User can create and edit or delete his comment
        switch ($attribute) {
            case self::CREATE:
                if($this->security->isGranted('ROLE_USER')) {
                    return true;
                    break;
                }
            case self::EDIT:
            case self::DELETE:
                if($user === $subject->getAuthorComment()) {
                    return true;
                    break;
                }
            default:
                return false;
        }
        
    }
}
