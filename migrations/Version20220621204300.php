<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220621204300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation ADD modepaiement_id INT NOT NULL');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D8CDA5193 FOREIGN KEY (modepaiement_id) REFERENCES mode_paiement (id)');
        $this->addSql('CREATE INDEX IDX_1981A66D8CDA5193 ON operation (modepaiement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D8CDA5193');
        $this->addSql('DROP INDEX IDX_1981A66D8CDA5193 ON operation');
        $this->addSql('ALTER TABLE operation DROP modepaiement_id');
    }
}
