<?php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $_em;

    /**
     * Hashes the plain password for the given user.
     * @var UserPasswordHasherInterface
     */
    private $_passwordEncoder;

    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordEncoder
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {
        if ($data->getPlainPassword()) {
            $data->setPassword($this->_passwordEncoder->hashPassword($data, $data->getPlainPassword()));
            $data->eraseCredentials();
        }

        $this->_em->persist($data);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $this->_entityManager->remove($data);
        $this->_entityManager->flush();
    }
}