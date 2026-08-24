<?php

namespace App\Security;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RecordVoter extends Voter
{
    public const MANAGE = 'RECORD_MANAGE';

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

        $recordId = (int) $subject;

        $sql = 'SELECT user_id
                    FROM categories AS c
                    INNER JOIN activities AS a ON a.category_id = c.id
                    INNER JOIN records AS r ON a.id = r.activity_id
                    WHERE r.id = :id'
        ;
        $userId = $this->connection->executeQuery($sql, ['id' => $recordId])->fetchOne();


        if (!$userId) {
            return false;
        }

        return (int) $userId === $user->getId();
    }
}

