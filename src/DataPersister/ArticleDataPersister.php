<?php

namespace App\DataPersister;

use App\Entity\Article;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
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

    public function __construct(
        private EntityManagerInterface $em,
        private SluggerInterface $slugger,
        RequestStack $requestStack
    ) {
        $this->_request = $requestStack->getCurrentRequest();
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
     */
    public function persist($data, array $context = [])
    {
        $data->setSlug($this->_slugger->slug(strtolower($data->getTitle())). '-' .uniqid());
        
        // Set the updatedAt value if it's not a POST request
        if ($this->_request->getMethod() !== 'POST') {
            $data->setUpdatedAt(new \DateTimeImmutable());
        }

        $categoryRepo = $this->_em->getRepository(Category::class);
        foreach ($data->getCategories() as $category) {
            $categoryName = $categoryRepo->findOneByName($category->getName());

            // if the tag exists, don't persist it
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
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }
}