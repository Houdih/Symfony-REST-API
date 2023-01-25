<?php

namespace App\DataPersister;

use App\Entity\Article;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $_em;

    /**
     * @param SluggerInterface
     */
    private $_slugger;

    /**
     * @param Request
     */
    private $_request;

    /**
     * @param Security
     */
    private $_security;

    public function __construct(
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        RequestStack $requestStack,
        Security $security
    ) {
        $this->_em = $em;
        $this->_slugger = $slugger;
        $this->_request = $requestStack->getCurrentRequest();
        $this->_security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Article;
    }

    /**
     * @param Article $data
     * @param array $context
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        // Update the slug
        $data->setSlug($this->_slugger->slug(strtolower($data->getTitle())). '-' .uniqid());

        // Set the author if it's a new article
        if($this->_request->getMethod() === 'POST') {
            $user = $this->_security->getUser();
            $userId = $user->getId();
            $userRepo = $this->_em->getRepository(User::class);
            $user = $userRepo->find($userId);
            $data->setAuthorArticle($user);
        }
        
        // Set the updatedAt value if it's a PUT or PATCH request
        if ($this->_request->getMethod() === 'PUT' || 'PATCH') {
            $data->setUpdatedAt(new \DateTimeImmutable());
        }

        $categoryRepo = $this->_em->getRepository(Category::class);
        foreach ($data->getCategories() as $category) {
            $categoryName = $categoryRepo->findOneByName($category->getName());

            // if the category exist, don't persist it
            if($categoryName !== null) {
                $data->removeCategory($category);
                $data->addCategory($categoryName);
            } else {
                $this->_em->persist($category);
            }
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