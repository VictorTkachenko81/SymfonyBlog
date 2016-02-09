<?php
namespace AppBundle\Services;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
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
        if (!in_array($attribute, array(self::EDIT, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof Comment) {
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
            case self::EDIT:
                return $this->canEdit($subject, $user, $token);
            case self::DELETE:
                return $this->canDelete($subject, $user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Comment $comment, User $user, $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        if ($this->decisionManager->decide($token, array('ROLE_MODERATOR'))) {
            return $comment->isAuthor($user);
        }

        return false;
    }

    private function canDelete(Comment $comment, User $user, $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        if ($this->decisionManager->decide($token, array('ROLE_MODERATOR'))) {
            if (!$this->isAdmin($comment) and $this->isArticleOwner($comment, $user)) return true;
            else return false;
        }

        return false;
    }

    protected function isAdmin(Comment $comment) {
        $roles = $comment->getUser()->getRoles();
        if (in_array("ROLE_ADMIN", $roles)) return true;
        else return false;
    }

    protected function isArticleOwner(Comment $comment, $user) {
        return $comment->getArticle()->isAuthor($user);
    }
}