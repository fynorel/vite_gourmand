<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260426200857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, nb_personnes INT NOT NULL, adresse LONGTEXT NOT NULL, date_prestation DATETIME NOT NULL, prix_menu NUMERIC(8, 2) NOT NULL, reduction NUMERIC(5, 2) NOT NULL, frais_livraison NUMERIC(6, 2) NOT NULL, prix_total NUMERIC(8, 2) NOT NULL, statut VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, mode_contact_annul VARCHAR(255) NOT NULL, motif_annulation LONGTEXT DEFAULT NULL, date_contact_annul DATE DEFAULT NULL, annule_par_id INT DEFAULT NULL, INDEX IDX_6EEAA67DF376B95 (annule_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF376B95 FOREIGN KEY (annule_par_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DF376B95');
        $this->addSql('DROP TABLE commande');
    }
}
