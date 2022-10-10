<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @param Security
     */
    private $_security;

    /**
     * @var EntityManagerInterface
     */
    private $_em;

    /**
     * @param Request
     */
    private $_request;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request,
        Security $security
    ) {
        $this->_em = $em;
        $this->_request = $request->getCurrentRequest();
        $this->_security = $security;
    }

    /**
     * vérifier si les données sont prises en charge par ce persistant de données
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
	return $data instanceof Comment;
    }

    /**
     * créer ou mettre à jour les données données
     * @param Comment $data
     */
    public function persist($data, array $context = [])
    {
        // Définir l'autheur du commentaire
        if($this->_request->getMethod() === 'POST') {
            $data->setAuthorComment($this->_security->getUser());
        }       

        // Modifie la valeur de 'updatedAt' si c'est une requête est en 'put' ou 'patch'
        if($this->_request->getMethod() === 'PATCH' || 'PUT') {
            $data->setUpdatedAt(new \DateTimeImmutable());
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
        $this->_em->remove($data);
        $this->_em->flush();
    }
}