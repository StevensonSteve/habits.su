<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250925110130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trucks (id UUID NOT NULL, vin VARCHAR(17) NOT NULL, brand VARCHAR(50) NOT NULL, model VARCHAR(50) NOT NULL, manufacture_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, mileage_initial INT NOT NULL, engine_type VARCHAR(255) NOT NULL, engine_capacity SMALLINT NOT NULL, engine_volume NUMERIC(4, 2) NOT NULL, purchase_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, color VARCHAR(30) DEFAULT NULL, license_plate VARCHAR(15) NOT NULL, max_weight INT NOT NULL, empty_weight INT NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_truck_vin ON trucks (vin)');
        $this->addSql('CREATE UNIQUE INDEX uniq_truck_license_plate ON trucks (license_plate)');
        $this->addSql('COMMENT ON COLUMN trucks.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN trucks.manufacture_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trucks.purchase_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trucks.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trucks.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE trucks');
    }
}
