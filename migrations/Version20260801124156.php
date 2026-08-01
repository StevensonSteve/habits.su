<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260801124156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todos ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE todos ADD CONSTRAINT FK_CD826255A76ED395 FOREIGN KEY (user_id) REFERENCES todo_users (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_CD826255A76ED395 ON todos (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todos DROP CONSTRAINT FK_CD826255A76ED395');
        $this->addSql('DROP INDEX IDX_CD826255A76ED395');
        $this->addSql('ALTER TABLE todos DROP user_id');
    }
}
