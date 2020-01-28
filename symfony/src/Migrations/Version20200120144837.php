<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200120144837 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('CREATE TABLE zone (
            uuid UUID NOT NULL,
            osm_id BIGINT NOT NULL,
            lat DOUBLE PRECISION NOT NULL,
            lng DOUBLE PRECISION NOT NULL,
            PRIMARY KEY(uuid)
        )');
        $this->addSql('CREATE TABLE polygon (
            uuid UUID NOT NULL,
            zone_id UUID NOT NULL,
            polygon Polygon NOT NULL,
            simplified Polygon NOT NULL,
            type INT NOT NULL,
            PRIMARY KEY(uuid)
        )');
        $this->addSql('CREATE INDEX IDX_C7A421129F2C3FAB ON polygon (zone_id)');
        $this->addSql('COMMENT ON COLUMN polygon.polygon IS \'(DC2Type:polygon)\'');
        $this->addSql('COMMENT ON COLUMN polygon.simplified IS \'(DC2Type:polygon)\'');
        $this->addSql('ALTER TABLE polygon ADD CONSTRAINT FK_C7A421129F2C3FAB
            FOREIGN KEY (zone_id) REFERENCES zone (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('ALTER TABLE polygon DROP CONSTRAINT FK_C7A421129F2C3FAB');
        $this->addSql('DROP TABLE zone');
        $this->addSql('DROP TABLE polygon');
    }
}
