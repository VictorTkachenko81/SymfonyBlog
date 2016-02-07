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
            if($entity->getFile()){
                $entity->setPicture(
                    $this->mediaHandler->fileUpload(
                        $entity->getFile(),
                        $entity->getUploadDir()
                    )
                );
            }
        }
        if($entity instanceof User) {
            if($entity->getFile()){
                $entity->setPhoto(
                    $this->mediaHandler->fileUpload(
                        $entity->getFile(),
                        $entity->getUploadDir()
                    )
                );
            }
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Article) {
            if($entity->getFile()){
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
            }
        }
        if($entity instanceof User) {
            if($entity->getFile()){
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

}