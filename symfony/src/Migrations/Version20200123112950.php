<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200123112950 extends AbstractMigration
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
        $this->addSql('CREATE INDEX IDX_E5F1AC331972DC04 ON pricing (from_zone_id)');
        $this->addSql('CREATE INDEX IDX_E5F1AC3311B4025E ON pricing (to_zone_id)');
        $this->addSql('ALTER TABLE pricing ADD CONSTRAINT FK_E5F1AC331972DC04 FOREIGN KEY (from_zone_id) REFERENCES zone (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pricing ADD CONSTRAINT FK_E5F1AC3311B4025E FOREIGN KEY (to_zone_id) REFERENCES zone (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE pricing');
    }
}
