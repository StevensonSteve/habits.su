<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251002184157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders ALTER total_amount TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE orders ALTER volume TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE orders RENAME COLUMN weigh TO weight');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE orders ALTER total_amount TYPE NUMERIC(10, 0)');
        $this->addSql('ALTER TABLE orders ALTER volume TYPE NUMERIC(4, 0)');
        $this->addSql('ALTER TABLE orders RENAME COLUMN weight TO weigh');
    }
}
