<?php

namespace App\Security;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ActivityVoter extends Voter
{
    public const MANAGE = 'ACTIVITY_MANAGE';

    public function __construct(
        private Connection $connection
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::MANAGE && (is_int($subject) || is_numeric($subject));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /** @var $user User */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $activityId = (int) $subject;

        $sql = 'SELECT user_id
                    FROM activities AS a
                    INNER JOIN categories AS c ON a.category_id = c.id
                    WHERE a.id = :id'
        ;
        $userId = $this->connection->executeQuery($sql, ['id' => $activityId])->fetchOne();

        if (!$userId) {
            return false;
        }

        return (int) $userId === $user->getId();
    }
}

