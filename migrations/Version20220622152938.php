<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220622152938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation ADD tiers_id INT NOT NULL');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D68B77723 FOREIGN KEY (tiers_id) REFERENCES tiers (id)');
        $this->addSql('CREATE INDEX IDX_1981A66D68B77723 ON operation (tiers_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D68B77723');
        $this->addSql('DROP INDEX IDX_1981A66D68B77723 ON operation');
        $this->addSql('ALTER TABLE operation DROP tiers_id');
    }
}
