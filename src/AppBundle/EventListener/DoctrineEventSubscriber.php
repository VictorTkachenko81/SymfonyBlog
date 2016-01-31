<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use AppBundle\Services\MediaHandler;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineEventSubscriber implements EventSubscriber
{

    protected $mediaHandler;

    public function __construct(MediaHandler $mediaHandler)
    {
        $this->mediaHandler = $mediaHandler;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postRemove'
        );
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Article) {

            $entity->setPicture(
                $this->mediaHandler->fileUpload(
                    $entity->getFile(),
                    $entity->getUploadDir()
                )
            );

            $entity->setSlug($this->slugify($entity->getTitle()));
            $entity->setCreatedAt(new \DateTime());
        }
        if($entity instanceof User) {

            $entity->setPhoto(
                $this->mediaHandler->fileUpload(
                    $entity->getFile(),
                    $entity->getUploadDir()
                )
            );
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Article) {

            $this->mediaHandler->clearCache(
                $entity->getTemp(),
                $entity->getUploadDir()
            );

            $entity->setPicture(
                $this->mediaHandler->fileUpload(
                    $entity->getFile(),
                    $entity->getUploadDir()
                )
            );

            $entity->setSlug($this->slugify($entity->getTitle()));
            $entity->setUpdatedAt(new \DateTime());
        }
        if($entity instanceof User) {

            $this->mediaHandler->clearCache(
                $entity->getTemp(),
                $entity->getUploadDir()
            );

            $entity->setPhoto(
                $this->mediaHandler->fileUpload(
                    $entity->getFile(),
                    $entity->getUploadDir()
                )
            );
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Article) {

            $this->mediaHandler->clearCache(
                $entity->getPicture(),
                $entity->getUploadDir()
            );
        }
        if($entity instanceof User) {

            $this->mediaHandler->clearCache(
                $entity->getPhoto(),
                $entity->getUploadDir()
            );
        }
    }

    protected function slugify($string)
    {
        return preg_replace(
            '/[^a-z0-9]/', '_', strtolower(trim(strip_tags($string)))
        );
    }

}