<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class ArticleVoter extends Voter
{
    const CREATE = 'ARTICLE_CREATE';
    const EDIT = 'ARTICLE_EDIT';
    const DELETE = 'ARTICLE_DELETE';

    private $security;

    public function __construct(Security $security) {    
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {        
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Article;
    }

    /**
     * @param $attribute
     * @param Article $subject
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

        // Author can create, edit or delete his Article
        switch ($attribute) {
            case self::CREATE:
                if($this->security->isGranted('ROLE_AUTHOR')) {
                    return true;
                    break;
                }
            case self::EDIT:
            case self::DELETE:
                if($user === $subject->getAuthorArticle()) {
                    return true;
                    break;
                }
            default:
                return false;
        }        
    }
}
