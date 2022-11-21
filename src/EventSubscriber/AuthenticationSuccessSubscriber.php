<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthenticationSuccessEventSubscriber implements EventSubscriberInterface
{
    private $_em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->_em = $em;
    }

    public function onLexikJwtAuthenticationOnAuthenticationSuccess($event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if(!$user instanceof User) {
            return;
        }
        $data['user'] = array(
            'id' => $user->getId(),
            'roles' => $user->getRoles(),
            'name' => $user->getName(),
        );

        $event->setData($data);

    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnAuthenticationSuccess',
        ];
    }
}