<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517123521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_token (id_token INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, expiry DATETIME NOT NULL, used TINYINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, id_utilisateur INT NOT NULL, INDEX idx_id_util (id_utilisateur), UNIQUE INDEX uk_token (token), PRIMARY KEY (id_token)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reset_token ADD CONSTRAINT FK_D7C8DC1950EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_token DROP FOREIGN KEY FK_D7C8DC1950EAE44');
        $this->addSql('DROP TABLE reset_token');
    }
}
