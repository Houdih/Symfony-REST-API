<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTSubscriber implements EventSubscriberInterface
{
    public function onEventLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event): void
    {
        // on ajoute l'email dans le jwt en plus de l'id
        $data = $event->getData();
        $user = $event->getUser();

        if($user instanceof User) {
            $data['email'] = $user->getEmail();
        }
        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onEventLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
