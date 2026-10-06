<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261006053019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('activities') || $schema->getTable('activities')->hasColumn('description_enabled')) {
            return;
        }

        $this->addSql('ALTER TABLE activities ADD description_enabled BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('activities') && $schema->getTable('activities')->hasColumn('description_enabled')) {
            $this->addSql('ALTER TABLE activities DROP description_enabled');
        }
    }
}
