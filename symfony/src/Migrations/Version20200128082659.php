<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200128082659 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE pricing (uuid UUID NOT NULL, from_zone_id UUID NOT NULL, to_zone_id UUID NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX idx_e5f1ac331972dc04 ON pricing (from_zone_id)');
        $this->addSql('CREATE INDEX idx_e5f1ac3311b4025e ON pricing (to_zone_id)');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE zone (uuid UUID NOT NULL, osm_id BIGINT NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, name VARCHAR(255) NOT NULL, area BIGINT DEFAULT 0 NOT NULL, PRIMARY KEY(uuid))');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE polygon (uuid UUID NOT NULL, zone_id UUID NOT NULL, polygon POLYGON NOT NULL, simplified POLYGON NOT NULL, type INT NOT NULL, line UUID DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX idx_c7a421129f2c3fab ON polygon (zone_id)');
        $this->addSql('COMMENT ON COLUMN polygon.polygon IS \'(DC2Type:polygon)\'');
        $this->addSql('COMMENT ON COLUMN polygon.simplified IS \'(DC2Type:polygon)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE pricing');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE zone');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE polygon');
    }
}
