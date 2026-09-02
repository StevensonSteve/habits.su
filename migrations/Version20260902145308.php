<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260902145308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE records ALTER amount TYPE NUMERIC(10, 2)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE records ALTER amount TYPE INT');
    }
}
