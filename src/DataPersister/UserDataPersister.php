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
     * @var UserPasswordHasherInterface
     */
    private $_passwordEncoder;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordEncoder
    ) {
        $this->_em = $em;
        $this->_passwordEncoder = $passwordEncoder;
    }

    /**
     * vérifier si les données sont prises en charge par ce persistant de données
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * créer ou mettre à jour les données données
     * @param User $data
     */
    public function persist($data, array $context = [])
    {
        // Encodage du mot de passe 
        if ($data->getPlainPassword()) {
            $data->setPassword($this->_passwordEncoder->hashPassword($data, $data->getPlainPassword()));
            $data->eraseCredentials();
        }

        $this->_em->persist($data);
        $this->_em->flush();
    }

    /**
     * supprimer les données données
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $this->_entityManager->remove($data);
        $this->_entityManager->flush();
    }
}