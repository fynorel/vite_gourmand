<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260426202822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique_statut (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(255) NOT NULL, changed_at DATETIME NOT NULL, commentaire LONGTEXT DEFAULT NULL, changed_by_id INT NOT NULL, INDEX IDX_2C2650E3828AD0A0 (changed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE historique_statut ADD CONSTRAINT FK_2C2650E3828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_statut DROP FOREIGN KEY FK_2C2650E3828AD0A0');
        $this->addSql('DROP TABLE historique_statut');
    }
}
