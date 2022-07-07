<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220621112106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banque ADD proprietaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE banque ADD CONSTRAINT FK_B1F6CB3C76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B1F6CB3C76C50E4A ON banque (proprietaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banque DROP FOREIGN KEY FK_B1F6CB3C76C50E4A');
        $this->addSql('DROP INDEX IDX_B1F6CB3C76C50E4A ON banque');
        $this->addSql('ALTER TABLE banque DROP proprietaire_id');
    }
}
