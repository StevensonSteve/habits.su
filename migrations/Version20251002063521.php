<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251002063521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clients (id UUID NOT NULL, name VARCHAR(255) NOT NULL, contact_person VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, address TEXT DEFAULT NULL, tax_number VARCHAR(20) NOT NULL, payment_terms VARCHAR(500) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_client_tax_number ON clients (tax_number)');
        $this->addSql('CREATE UNIQUE INDEX uniq_client_email ON clients (email)');
        $this->addSql('COMMENT ON COLUMN clients.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE clients');
    }
}
