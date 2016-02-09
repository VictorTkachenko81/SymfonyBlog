<?php
namespace AppBundle\Services;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'delete';
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::CREATE, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof Article) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user, $token);
            case self::EDIT:
                return $this->canEdit($subject, $user, $token);
            case self::CREATE:
                return $this->canCreate($subject, $user, $token);
            case self::DELETE:
                return $this->canDelete($subject, $user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Article $article, User $user, $token)
    {
        if ($this->canEdit($article, $user, $token)) {
            return true;
        }

        return true;
    }

    private function canEdit(Article $article, User $user, $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        if ($this->decisionManager->decide($token, array('ROLE_MODERATOR'))) {
            return $article->isAuthor($user);
        }

        return false;
    }

    private function canCreate(Article $article, User $user, $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN', 'ROLE_MODERATOR'))) {
            return true;
        }

        return false;
    }

    private function canDelete(Article $article, User $user, $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        if ($this->decisionManager->decide($token, array('ROLE_MODERATOR'))) {
            return $article->isAuthor($user);
        }

        return false;
    }
}