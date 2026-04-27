<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260426205343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT NOT NULL, statut VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, date_moderation DATETIME NOT NULL, validate_par_id INT DEFAULT NULL, INDEX IDX_8F91ABF0AA25E7F (validate_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0AA25E7F FOREIGN KEY (validate_par_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0AA25E7F');
        $this->addSql('DROP TABLE avis');
    }
}
