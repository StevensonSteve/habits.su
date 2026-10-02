<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261002153235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description field to records table';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('records') || $schema->getTable('records')->hasColumn('description')) {
            return;
        }

        $this->addSql('ALTER TABLE records ADD description VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('records') && $schema->getTable('records')->hasColumn('description')) {
            $this->addSql('ALTER TABLE records DROP description');
        }
    }
}
