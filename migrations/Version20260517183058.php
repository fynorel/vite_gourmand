<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517183058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allergene (id_allergene INT AUTO_INCREMENT NOT NULL, nom VARCHAR(80) NOT NULL, code_eu VARCHAR(10) DEFAULT NULL, UNIQUE INDEX uk_nom (nom), PRIMARY KEY (id_allergene)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE avis (id_avis INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT NOT NULL, statut VARCHAR(20) NOT NULL, date_creation DATETIME NOT NULL, date_moderation DATETIME DEFAULT NULL, id_commande INT NOT NULL, id_utilisateur INT NOT NULL, validate_par INT DEFAULT NULL, INDEX idx_id_utilisateur (id_utilisateur), INDEX idx_statut (statut), INDEX idx_validate_par (validate_par), UNIQUE INDEX uk_id_commande (id_commande), PRIMARY KEY (id_avis)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commande (id_commande INT AUTO_INCREMENT NOT NULL, nb_personnes INT NOT NULL, adresse LONGTEXT NOT NULL, date_prestation DATETIME NOT NULL, prix_menu NUMERIC(8, 2) NOT NULL, reduction NUMERIC(5, 2) NOT NULL, frais_livraison NUMERIC(6, 2) NOT NULL, prix_total NUMERIC(8, 2) NOT NULL, statut VARCHAR(30) NOT NULL, date_creation DATETIME NOT NULL, mode_contact_annul VARCHAR(10) DEFAULT NULL, motif_annulation LONGTEXT DEFAULT NULL, date_contact_annul DATE DEFAULT NULL, id_utilisateur INT NOT NULL, id_menu INT NOT NULL, annule_par INT DEFAULT NULL, INDEX idx_id_util (id_utilisateur), INDEX idx_id_menu (id_menu), INDEX idx_statut (statut), INDEX idx_date_presta (date_prestation), INDEX idx_annule_par (annule_par), INDEX idx_util_statut (id_utilisateur, statut), INDEX idx_menu_statut (id_menu, statut), PRIMARY KEY (id_commande)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE entreprise (id_entreprise INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, adresse LONGTEXT DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, professionnalisme LONGTEXT DEFAULT NULL, PRIMARY KEY (id_entreprise)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE historique_statut (id_historique INT AUTO_INCREMENT NOT NULL, statut VARCHAR(30) NOT NULL, changed_at DATETIME NOT NULL, commentaire LONGTEXT DEFAULT NULL, id_commande INT NOT NULL, changed_by INT NOT NULL, INDEX idx_id_commande (id_commande), INDEX idx_changed_at (changed_at), INDEX idx_changed_by (changed_by), PRIMARY KEY (id_historique)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE horaire (id_horaire INT AUTO_INCREMENT NOT NULL, jour_semaine INT NOT NULL, heure_ouverture TIME DEFAULT NULL, heure_fermeture TIME DEFAULT NULL, est_ferme TINYINT NOT NULL, id_entreprise INT NOT NULL, INDEX idx_id_entreprise (id_entreprise), UNIQUE INDEX uk_entreprise_jour (id_entreprise, jour_semaine), PRIMARY KEY (id_horaire)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE image_menu (id_image INT AUTO_INCREMENT NOT NULL, url VARCHAR(500) NOT NULL, alt VARCHAR(255) NOT NULL, ordre INT NOT NULL, id_menu INT NOT NULL, INDEX idx_id_menu (id_menu), INDEX idx_id_menu_ordre (id_menu, ordre), PRIMARY KEY (id_image)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu (id_menu INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, theme VARCHAR(20) NOT NULL, regime VARCHAR(20) NOT NULL, nb_personnes_min INT NOT NULL, prix NUMERIC(8, 2) NOT NULL, stock INT NOT NULL, conditions LONGTEXT DEFAULT NULL, actif TINYINT NOT NULL, date_creation DATETIME NOT NULL, INDEX idx_actif (actif), INDEX idx_theme (theme), INDEX idx_regime (regime), INDEX idx_prix (prix), INDEX idx_actif_theme (actif, theme), PRIMARY KEY (id_menu)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_plat (id_menu INT NOT NULL, id_plat INT NOT NULL, INDEX IDX_E8775249F6252691 (id_menu), INDEX IDX_E8775249AB18BE05 (id_plat), PRIMARY KEY (id_menu, id_plat)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE plat (id_plat INT AUTO_INCREMENT NOT NULL, nom VARCHAR(120) NOT NULL, type VARCHAR(20) NOT NULL, description LONGTEXT DEFAULT NULL, actif TINYINT NOT NULL, INDEX idx_type (type), INDEX idx_actif (actif), PRIMARY KEY (id_plat)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE plat_allergene (id_plat INT NOT NULL, id_allergene INT NOT NULL, INDEX IDX_6FA44BBFAB18BE05 (id_plat), INDEX IDX_6FA44BBF131696C2 (id_allergene), PRIMARY KEY (id_plat, id_allergene)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_token (id_token INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, expiry DATETIME NOT NULL, used TINYINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, id_utilisateur INT NOT NULL, INDEX idx_id_util (id_utilisateur), UNIQUE INDEX uk_token (token), PRIMARY KEY (id_token)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, expire DATETIME NOT NULL, created_at DATETIME NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_D044D5D4FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(80) NOT NULL, nom VARCHAR(80) NOT NULL, mail VARCHAR(255) NOT NULL, gsm VARCHAR(20) DEFAULT NULL, adresse LONGTEXT DEFAULT NULL, mdp_hash VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, actif TINYINT NOT NULL, compteur_authentification INT NOT NULL, date_creation DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF03E314AE8 FOREIGN KEY (id_commande) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF050EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A7901B2A FOREIGN KEY (validate_par) REFERENCES utilisateur (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF6252691 FOREIGN KEY (id_menu) REFERENCES menu (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D19A91A45 FOREIGN KEY (annule_par) REFERENCES utilisateur (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE historique_statut ADD CONSTRAINT FK_2C2650E33E314AE8 FOREIGN KEY (id_commande) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE historique_statut ADD CONSTRAINT FK_2C2650E310BC6D9F FOREIGN KEY (changed_by) REFERENCES utilisateur (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE horaire ADD CONSTRAINT FK_BBC83DB6A8937AB7 FOREIGN KEY (id_entreprise) REFERENCES entreprise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_menu ADD CONSTRAINT FK_8F3FD00DF6252691 FOREIGN KEY (id_menu) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_plat ADD CONSTRAINT FK_E8775249F6252691 FOREIGN KEY (id_menu) REFERENCES menu (id_menu) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_plat ADD CONSTRAINT FK_E8775249AB18BE05 FOREIGN KEY (id_plat) REFERENCES plat (id_plat) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plat_allergene ADD CONSTRAINT FK_6FA44BBFAB18BE05 FOREIGN KEY (id_plat) REFERENCES plat (id_plat) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plat_allergene ADD CONSTRAINT FK_6FA44BBF131696C2 FOREIGN KEY (id_allergene) REFERENCES allergene (id_allergene) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE reset_token ADD CONSTRAINT FK_D7C8DC1950EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF03E314AE8');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF050EAE44');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A7901B2A');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D50EAE44');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DF6252691');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D19A91A45');
        $this->addSql('ALTER TABLE historique_statut DROP FOREIGN KEY FK_2C2650E33E314AE8');
        $this->addSql('ALTER TABLE historique_statut DROP FOREIGN KEY FK_2C2650E310BC6D9F');
        $this->addSql('ALTER TABLE horaire DROP FOREIGN KEY FK_BBC83DB6A8937AB7');
        $this->addSql('ALTER TABLE image_menu DROP FOREIGN KEY FK_8F3FD00DF6252691');
        $this->addSql('ALTER TABLE menu_plat DROP FOREIGN KEY FK_E8775249F6252691');
        $this->addSql('ALTER TABLE menu_plat DROP FOREIGN KEY FK_E8775249AB18BE05');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBFAB18BE05');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBF131696C2');
        $this->addSql('ALTER TABLE reset_token DROP FOREIGN KEY FK_D7C8DC1950EAE44');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4FB88E14F');
        $this->addSql('DROP TABLE allergene');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE historique_statut');
        $this->addSql('DROP TABLE horaire');
        $this->addSql('DROP TABLE image_menu');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_plat');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE plat_allergene');
        $this->addSql('DROP TABLE reset_token');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE utilisateur');
    }
}
