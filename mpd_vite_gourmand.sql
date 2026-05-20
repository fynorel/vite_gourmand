-- ============================================================================
-- MPD — Modèle Physique de Données — Vite & Gourmand
-- Système : MySQL 8.0+ / MariaDB 10.6+
-- Encodage : UTF8MB4 / Collation : utf8mb4_unicode_ci
-- Moteur : InnoDB (transactions, FK, ACID)
-- Auteur : FastDev
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ============================================================================
-- 0. CRÉATION DE LA BASE
-- ============================================================================

CREATE DATABASE IF NOT EXISTS vite_gourmand
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE vite_gourmand;

-- ============================================================================
-- 1. TABLE : utilisateur
-- ============================================================================

CREATE TABLE utilisateur (
    id_utilisateur   INT            NOT NULL AUTO_INCREMENT,
    nom              VARCHAR(80)    NOT NULL,
    prenom           VARCHAR(80)    NOT NULL,
    mail             VARCHAR(255)   NOT NULL,
    gsm              VARCHAR(20)    DEFAULT NULL,
    adresse          TEXT           DEFAULT NULL,
    mdp_hash         VARCHAR(255)   NOT NULL                       COMMENT 'Hash bcrypt, cost >= 12',
    role             ENUM(
                         'UTILISATEUR',
                         'EMPLOYE',
                         'ADMINISTRATEUR'
                     )              NOT NULL DEFAULT 'UTILISATEUR',
    actif            TINYINT(1)     NOT NULL DEFAULT 1,
    failed_attempts  INT            NOT NULL DEFAULT 0,
    date_creation    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Clé primaire
    PRIMARY KEY (id_utilisateur),

    -- Contraintes d'unicité
    UNIQUE KEY uk_mail (mail),

    -- Index de performance
    INDEX idx_role   (role),
    INDEX idx_actif  (actif),
    INDEX idx_mail_actif (mail, actif)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Comptes utilisateurs : clients, employés, administrateur';

-- ============================================================================
-- 2. TABLE : session
-- ============================================================================

CREATE TABLE session (
    id_session       INT            NOT NULL AUTO_INCREMENT,
    id_utilisateur   INT            NOT NULL,
    token            VARCHAR(255)   NOT NULL                       COMMENT 'random_bytes(32) encodé en hex',
    role             ENUM(
                         'UTILISATEUR',
                         'EMPLOYE',
                         'ADMINISTRATEUR'
                     )              NOT NULL,
    expiry           DATETIME       NOT NULL,
    created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id_session),
    UNIQUE KEY uk_token (token),
    INDEX idx_id_util  (id_utilisateur),
    INDEX idx_expiry   (expiry),

    CONSTRAINT fk_session_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur (id_utilisateur)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Sessions actives — TTL géré par le service applicatif';

-- ============================================================================
-- 3. TABLE : reset_token
-- ============================================================================

CREATE TABLE reset_token (
    id_token         INT            NOT NULL AUTO_INCREMENT,
    id_utilisateur   INT            NOT NULL,
    token            VARCHAR(255)   NOT NULL                       COMMENT 'random_bytes(32) encodé en hex, usage unique',
    expiry           DATETIME       NOT NULL                       COMMENT 'NOW() + INTERVAL 1 HOUR',
    used             TINYINT(1)     NOT NULL DEFAULT 0             COMMENT '0=disponible, 1=consommé',
    created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id_token),
    UNIQUE KEY uk_token (token),
    INDEX idx_id_util (id_utilisateur),

    CONSTRAINT fk_reset_token_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur (id_utilisateur)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tokens de réinitialisation de mot de passe';

-- ============================================================================
-- 4. TABLE : menu
-- ============================================================================

CREATE TABLE menu (
    id_menu           INT             NOT NULL AUTO_INCREMENT,
    titre             VARCHAR(150)    NOT NULL,
    description       TEXT            DEFAULT NULL,
    theme             ENUM(
                          'NOEL',
                          'PAQUES',
                          'CLASSIQUE',
                          'EVENEMENT',
                          'MARIAGE',
                          'ENTREPRISE'
                      )               NOT NULL DEFAULT 'CLASSIQUE',
    regime            ENUM(
                          'CLASSIQUE',
                          'VEGETARIEN',
                          'VEGAN',
                          'SANS_GLUTEN'
                      )               NOT NULL DEFAULT 'CLASSIQUE',
    nb_personnes_min  INT             NOT NULL,
    prix              DECIMAL(8,2)    NOT NULL,
    stock             INT             NOT NULL DEFAULT 0,
    conditions        TEXT            DEFAULT NULL,
    actif             TINYINT(1)      NOT NULL DEFAULT 1,
    date_creation     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id_menu),
    INDEX idx_actif        (actif),
    INDEX idx_theme        (theme),
    INDEX idx_regime       (regime),
    INDEX idx_actif_theme  (actif, theme),
    INDEX idx_prix         (prix),

    CONSTRAINT chk_prix_positif         CHECK (prix > 0),
    CONSTRAINT chk_nb_personnes_positif CHECK (nb_personnes_min > 0),
    CONSTRAINT chk_stock_non_negatif    CHECK (stock >= 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catalogue de menus proposés par Vite & Gourmand';

-- ============================================================================
-- 5. TABLE : plat
-- ============================================================================

CREATE TABLE plat (
    id_plat      INT            NOT NULL AUTO_INCREMENT,
    nom          VARCHAR(120)   NOT NULL,
    type         ENUM(
                     'ENTREE',
                     'PLAT',
                     'DESSERT',
                     'AMUSE_BOUCHE'
                 )              NOT NULL,
    description  TEXT           DEFAULT NULL,
    actif        TINYINT(1)     NOT NULL DEFAULT 1,

    PRIMARY KEY (id_plat),
    INDEX idx_type  (type),
    INDEX idx_actif (actif)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Composants réutilisables des menus';

-- ============================================================================
-- 6. TABLE : allergene
-- ============================================================================

CREATE TABLE allergene (
    id_allergene  INT           NOT NULL AUTO_INCREMENT,
    nom           VARCHAR(80)   NOT NULL,
    code_eu       VARCHAR(10)   DEFAULT NULL                       COMMENT 'Code réglementaire EU (Règlement 1169/2011)',

    PRIMARY KEY (id_allergene),
    UNIQUE KEY uk_nom (nom)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Référentiel des 14 allergènes réglementaires EU';

-- ============================================================================
-- 7. TABLE : image_menu
-- ============================================================================

CREATE TABLE image_menu (
    id_image  INT            NOT NULL AUTO_INCREMENT,
    id_menu   INT            NOT NULL,
    url       VARCHAR(500)   NOT NULL,
    alt       VARCHAR(255)   NOT NULL                              COMMENT 'Texte alternatif obligatoire (RGAA)',
    ordre     INT            NOT NULL DEFAULT 0,

    PRIMARY KEY (id_image),
    INDEX idx_id_menu       (id_menu),
    INDEX idx_id_menu_ordre (id_menu, ordre),

    CONSTRAINT fk_image_menu_menu
        FOREIGN KEY (id_menu)
        REFERENCES menu (id_menu)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Galerie d''images associées aux menus';

-- ============================================================================
-- 8. TABLE DE LIAISON : menu_plat  (N-N entre menu et plat)
-- ============================================================================

CREATE TABLE menu_plat (
    id_menu  INT  NOT NULL,
    id_plat  INT  NOT NULL,

    PRIMARY KEY (id_menu, id_plat),
    INDEX idx_id_plat (id_plat),

    CONSTRAINT fk_menu_plat_menu
        FOREIGN KEY (id_menu)
        REFERENCES menu (id_menu)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_menu_plat_plat
        FOREIGN KEY (id_plat)
        REFERENCES plat (id_plat)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Table de liaison N-N menu ↔ plat';

-- ============================================================================
-- 9. TABLE DE LIAISON : plat_allergene  (N-N entre plat et allergene)
-- ============================================================================

CREATE TABLE plat_allergene (
    id_plat      INT  NOT NULL,
    id_allergene INT  NOT NULL,

    PRIMARY KEY (id_plat, id_allergene),
    INDEX idx_id_allergene (id_allergene),

    CONSTRAINT fk_plat_allergene_plat
        FOREIGN KEY (id_plat)
        REFERENCES plat (id_plat)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_plat_allergene_allergene
        FOREIGN KEY (id_allergene)
        REFERENCES allergene (id_allergene)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Table de liaison N-N plat ↔ allergene';

-- ============================================================================
-- 10. TABLE : commande
-- ============================================================================

CREATE TABLE commande (
    id_commande         INT             NOT NULL AUTO_INCREMENT,
    id_utilisateur      INT             NOT NULL,
    id_menu             INT             NOT NULL,
    nb_personnes        INT             NOT NULL,
    adresse             TEXT            NOT NULL,
    date_prestation     DATETIME        NOT NULL,
    prix_menu           DECIMAL(8,2)    NOT NULL                   COMMENT 'Prix capturé à la commande — immuable',
    reduction           DECIMAL(5,2)    NOT NULL DEFAULT 0.00      COMMENT '10% si nb_personnes >= nb_personnes_min + 5',
    frais_livraison     DECIMAL(6,2)    NOT NULL DEFAULT 0.00      COMMENT '5€ + 0,59€/km si hors Bordeaux',
    prix_total          DECIMAL(8,2)    NOT NULL                   COMMENT 'prix_menu - reduction + frais_livraison',
    statut              ENUM(
                            'EN_ATTENTE',
                            'ACCEPTE',
                            'EN_PREPARATION',
                            'EN_COURS_LIVRAISON',
                            'LIVRE',
                            'RETOUR_MATERIEL',
                            'TERMINEE',
                            'ANNULEE'
                        )               NOT NULL DEFAULT 'EN_ATTENTE',
    date_creation       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    mode_contact_annul  ENUM('GSM','MAIL')  DEFAULT NULL           COMMENT 'Renseigné si annulation par employé',
    motif_annulation    TEXT            DEFAULT NULL               COMMENT 'Obligatoire si annulation par employé',
    date_contact_annul  DATE            DEFAULT NULL               COMMENT 'Date du contact préalable à l''annulation',
    annule_par          INT             DEFAULT NULL               COMMENT 'FK vers l''employé ayant annulé',

    PRIMARY KEY (id_commande),
    INDEX idx_id_util        (id_utilisateur),
    INDEX idx_id_menu        (id_menu),
    INDEX idx_statut         (statut),
    INDEX idx_date_presta    (date_prestation),
    INDEX idx_annule_par     (annule_par),
    INDEX idx_util_statut    (id_utilisateur, statut),
    INDEX idx_menu_statut    (id_menu, statut),

    CONSTRAINT chk_nb_personnes_pos CHECK (nb_personnes > 0),
    CONSTRAINT chk_reduction_pos    CHECK (reduction >= 0),
    CONSTRAINT chk_frais_pos        CHECK (frais_livraison >= 0),
    CONSTRAINT chk_prix_total_pos   CHECK (prix_total >= 0),

    CONSTRAINT fk_commande_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur (id_utilisateur)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_commande_menu
        FOREIGN KEY (id_menu)
        REFERENCES menu (id_menu)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_commande_annule_par
        FOREIGN KEY (annule_par)
        REFERENCES utilisateur (id_utilisateur)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Commandes clients — table centrale du workflow métier';

-- ============================================================================
-- 11. TABLE : historique_statut
-- ============================================================================

CREATE TABLE historique_statut (
    id_historique  INT             NOT NULL AUTO_INCREMENT,
    id_commande    INT             NOT NULL,
    statut         ENUM(
                       'EN_ATTENTE',
                       'ACCEPTE',
                       'EN_PREPARATION',
                       'EN_COURS_LIVRAISON',
                       'LIVRE',
                       'RETOUR_MATERIEL',
                       'TERMINEE',
                       'ANNULEE'
                   )               NOT NULL,
    changed_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    changed_by     INT             NOT NULL,
    commentaire    TEXT            DEFAULT NULL,

    PRIMARY KEY (id_historique),
    INDEX idx_id_commande (id_commande),
    INDEX idx_changed_at  (changed_at),
    INDEX idx_changed_by  (changed_by),

    CONSTRAINT fk_historique_commande
        FOREIGN KEY (id_commande)
        REFERENCES commande (id_commande)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_historique_changed_by
        FOREIGN KEY (changed_by)
        REFERENCES utilisateur (id_utilisateur)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Timeline immuable des changements de statut — INSERT ONLY';

-- ============================================================================
-- 12. TABLE : avis
-- ============================================================================

CREATE TABLE avis (
    id_avis          INT            NOT NULL AUTO_INCREMENT,
    id_commande      INT            NOT NULL,
    id_utilisateur   INT            NOT NULL,
    note             TINYINT        NOT NULL,
    commentaire      TEXT           NOT NULL,
    statut           ENUM(
                         'EN_ATTENTE',
                         'VALIDE',
                         'REFUSE'
                     )              NOT NULL DEFAULT 'EN_ATTENTE',
    validate_par     INT            DEFAULT NULL,
    date_creation    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_moderation  DATETIME       DEFAULT NULL,

    PRIMARY KEY (id_avis),
    UNIQUE KEY uk_id_commande   (id_commande)                     COMMENT '1 seul avis par commande',
    INDEX idx_id_utilisateur    (id_utilisateur),
    INDEX idx_statut            (statut),
    INDEX idx_validate_par      (validate_par),

    CONSTRAINT chk_note_range CHECK (note BETWEEN 1 AND 5),

    CONSTRAINT fk_avis_commande
        FOREIGN KEY (id_commande)
        REFERENCES commande (id_commande)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_avis_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur (id_utilisateur)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_avis_validate_par
        FOREIGN KEY (validate_par)
        REFERENCES utilisateur (id_utilisateur)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Avis clients — soumis à modération avant publication';

-- ============================================================================
-- 13. TABLE : entreprise
-- ============================================================================

CREATE TABLE entreprise (
    id_entreprise     INT            NOT NULL AUTO_INCREMENT,
    nom               VARCHAR(150)   NOT NULL,
    description       TEXT           DEFAULT NULL,
    adresse           TEXT           DEFAULT NULL,
    telephone         VARCHAR(20)    DEFAULT NULL,
    email             VARCHAR(255)   DEFAULT NULL,
    professionnalisme TEXT           DEFAULT NULL,

    PRIMARY KEY (id_entreprise)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Données de présentation de l''entreprise (singleton — 1 ligne)';

-- ============================================================================
-- 14. TABLE : horaire
-- ============================================================================

CREATE TABLE horaire (
    id_horaire       INT         NOT NULL AUTO_INCREMENT,
    id_entreprise    INT         NOT NULL,
    jour_semaine     TINYINT     NOT NULL                          COMMENT '1=Lundi … 7=Dimanche (ISO 8601)',
    heure_ouverture  TIME        DEFAULT NULL                      COMMENT 'NULL si fermé ce jour',
    heure_fermeture  TIME        DEFAULT NULL                      COMMENT 'NULL si fermé ce jour',
    est_ferme        TINYINT(1)  NOT NULL DEFAULT 0,

    PRIMARY KEY (id_horaire),
    UNIQUE KEY uk_entreprise_jour (id_entreprise, jour_semaine),
    INDEX idx_id_entreprise (id_entreprise),

    CONSTRAINT chk_jour_semaine CHECK (jour_semaine BETWEEN 1 AND 7),

    CONSTRAINT fk_horaire_entreprise
        FOREIGN KEY (id_entreprise)
        REFERENCES entreprise (id_entreprise)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Horaires hebdomadaires de l''entreprise';

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- 15. TRIGGERS
-- ============================================================================

DELIMITER $$

-- ── Trigger : stock auto-décrémenté à la création d'une commande ─────────────
CREATE TRIGGER trg_commande_after_insert
AFTER INSERT ON commande
FOR EACH ROW
BEGIN
    IF NEW.statut = 'EN_ATTENTE' THEN
        UPDATE menu SET stock = stock - 1
        WHERE id_menu = NEW.id_menu;
    END IF;
END$$

-- ── Trigger : stock restitué si commande annulée (UPDATE statut → ANNULEE) ──
CREATE TRIGGER trg_commande_after_update
AFTER UPDATE ON commande
FOR EACH ROW
BEGIN
    -- Restitution du stock lors d'une annulation
    IF NEW.statut = 'ANNULEE' AND OLD.statut <> 'ANNULEE' THEN
        UPDATE menu SET stock = stock + 1
        WHERE id_menu = NEW.id_menu;
    END IF;
    -- Insertion automatique dans historique_statut si le statut change
    IF NEW.statut <> OLD.statut THEN
        INSERT INTO historique_statut (id_commande, statut, changed_at, changed_by)
        VALUES (NEW.id_commande, NEW.statut, NOW(),
                COALESCE(NEW.annule_par, NEW.id_utilisateur));
    END IF;
END$$

-- ── Trigger : insertion initiale dans historique_statut (EN_ATTENTE) ─────────
CREATE TRIGGER trg_historique_after_commande_insert
AFTER INSERT ON commande
FOR EACH ROW
BEGIN
    INSERT INTO historique_statut (id_commande, statut, changed_at, changed_by)
    VALUES (NEW.id_commande, 'EN_ATTENTE', NOW(), NEW.id_utilisateur);
END$$

-- ── Trigger : date_moderation auto si statut avis change ─────────────────────
CREATE TRIGGER trg_avis_before_update
BEFORE UPDATE ON avis
FOR EACH ROW
BEGIN
    IF NEW.statut <> OLD.statut AND NEW.statut IN ('VALIDE', 'REFUSE') THEN
        SET NEW.date_moderation = NOW();
    END IF;
END$$

-- ── Trigger : bloc suppression historique_statut (immuable) ──────────────────
CREATE TRIGGER trg_historique_before_delete
BEFORE DELETE ON historique_statut
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Suppression interdite : historique_statut est une table immuable (audit trail).';
END$$

-- ── Trigger : bloc mise à jour historique_statut (immuable) ──────────────────
CREATE TRIGGER trg_historique_before_update
BEFORE UPDATE ON historique_statut
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Mise à jour interdite : historique_statut est une table immuable (audit trail).';
END$$

DELIMITER ;

-- ============================================================================
-- 16. VUES
-- ============================================================================

-- ── Vue : menus disponibles avec nombre de plats ─────────────────────────────
CREATE OR REPLACE VIEW v_menus_actifs AS
SELECT
    m.id_menu,
    m.titre,
    m.theme,
    m.regime,
    m.nb_personnes_min,
    m.prix,
    m.stock,
    COUNT(DISTINCT mp.id_plat) AS nb_plats
FROM menu m
LEFT JOIN menu_plat mp ON mp.id_menu = m.id_menu
WHERE m.actif = 1
GROUP BY m.id_menu;

-- ── Vue : commandes en cours (non terminées / non annulées) ──────────────────
CREATE OR REPLACE VIEW v_commandes_actives AS
SELECT
    c.id_commande,
    c.id_utilisateur,
    CONCAT(u.prenom, ' ', u.nom) AS client,
    m.titre                      AS menu_titre,
    c.nb_personnes,
    c.date_prestation,
    c.prix_total,
    c.statut,
    c.date_creation
FROM commande c
JOIN utilisateur u ON u.id_utilisateur = c.id_utilisateur
JOIN menu        m ON m.id_menu        = c.id_menu
WHERE c.statut NOT IN ('TERMINEE', 'ANNULEE');

-- ── Vue : avis publiés (validés uniquement) ───────────────────────────────────
CREATE OR REPLACE VIEW v_avis_publies AS
SELECT
    a.id_avis,
    CONCAT(u.prenom, ' ', LEFT(u.nom, 1), '.') AS auteur,
    m.titre                                      AS menu_titre,
    a.note,
    a.commentaire,
    a.date_creation
FROM avis a
JOIN commande    c ON c.id_commande    = a.id_commande
JOIN menu        m ON m.id_menu        = c.id_menu
JOIN utilisateur u ON u.id_utilisateur = a.id_utilisateur
WHERE a.statut = 'VALIDE'
ORDER BY a.date_creation DESC;

-- ── Vue : note moyenne par menu ──────────────────────────────────────────────
CREATE OR REPLACE VIEW v_note_moyenne_menu AS
SELECT
    m.id_menu,
    m.titre,
    ROUND(AVG(a.note), 1) AS note_moyenne,
    COUNT(a.id_avis)      AS nb_avis
FROM menu m
JOIN commande c ON c.id_menu     = m.id_menu
JOIN avis     a ON a.id_commande = c.id_commande
WHERE a.statut = 'VALIDE'
GROUP BY m.id_menu, m.titre;

-- ── Vue : tableau de bord employé ────────────────────────────────────────────
CREATE OR REPLACE VIEW v_dashboard_employe AS
SELECT
    (SELECT COUNT(*) FROM commande WHERE statut = 'EN_ATTENTE')        AS nb_en_attente,
    (SELECT COUNT(*) FROM commande WHERE statut = 'ACCEPTE')           AS nb_acceptees,
    (SELECT COUNT(*) FROM commande WHERE statut = 'EN_PREPARATION')    AS nb_en_preparation,
    (SELECT COUNT(*) FROM commande WHERE statut = 'EN_COURS_LIVRAISON') AS nb_en_livraison,
    (SELECT COUNT(*) FROM commande WHERE statut = 'RETOUR_MATERIEL')   AS nb_retour_materiel,
    (SELECT COUNT(*) FROM avis     WHERE statut = 'EN_ATTENTE')        AS nb_avis_a_moderer;

-- ============================================================================
-- 17. PROCÉDURES STOCKÉES
-- ============================================================================

DELIMITER $$

-- ── Procédure : calculer le prix total d'une commande ────────────────────────
CREATE PROCEDURE sp_calculer_prix(
    IN  p_id_menu      INT,
    IN  p_nb_personnes INT,
    IN  p_adresse      TEXT,
    OUT p_prix_menu    DECIMAL(8,2),
    OUT p_reduction    DECIMAL(5,2),
    OUT p_frais_liv    DECIMAL(6,2),
    OUT p_total        DECIMAL(8,2)
)
BEGIN
    DECLARE v_prix          DECIMAL(8,2);
    DECLARE v_nb_min        INT;
    DECLARE v_bordeaux      TINYINT DEFAULT 1;

    SELECT prix, nb_personnes_min INTO v_prix, v_nb_min
    FROM menu WHERE id_menu = p_id_menu;

    SET p_prix_menu = v_prix;

    -- Réduction 10% si nb_personnes >= nb_personnes_min + 5
    IF p_nb_personnes >= (v_nb_min + 5) THEN
        SET p_reduction = ROUND(v_prix * 0.10, 2);
    ELSE
        SET p_reduction = 0.00;
    END IF;

    -- Frais de livraison : 0 si Bordeaux, sinon 5€ + 0,59€/km
    -- NOTE : le calcul km réel est effectué côté applicatif (API géocodage)
    -- Ici on stocke la valeur déjà calculée passée par le service
    SET p_frais_liv = 0.00; -- Remplacé par la valeur calculée côté PHP

    SET p_total = p_prix_menu - p_reduction + p_frais_liv;
END$$

-- ── Procédure : valider une transition de statut ──────────────────────────────
CREATE PROCEDURE sp_valider_transition(
    IN  p_statut_actuel  VARCHAR(30),
    IN  p_statut_cible   VARCHAR(30),
    OUT p_valide         TINYINT
)
BEGIN
    SET p_valide = CASE
        WHEN p_statut_actuel = 'EN_ATTENTE'         AND p_statut_cible = 'ACCEPTE'             THEN 1
        WHEN p_statut_actuel = 'EN_ATTENTE'         AND p_statut_cible = 'ANNULEE'             THEN 1
        WHEN p_statut_actuel = 'ACCEPTE'            AND p_statut_cible = 'EN_PREPARATION'      THEN 1
        WHEN p_statut_actuel = 'ACCEPTE'            AND p_statut_cible = 'ANNULEE'             THEN 1
        WHEN p_statut_actuel = 'EN_PREPARATION'     AND p_statut_cible = 'EN_COURS_LIVRAISON'  THEN 1
        WHEN p_statut_actuel = 'EN_PREPARATION'     AND p_statut_cible = 'ANNULEE'             THEN 1
        WHEN p_statut_actuel = 'EN_COURS_LIVRAISON' AND p_statut_cible = 'LIVRE'               THEN 1
        WHEN p_statut_actuel = 'EN_COURS_LIVRAISON' AND p_statut_cible = 'ANNULEE'             THEN 1
        WHEN p_statut_actuel = 'LIVRE'              AND p_statut_cible = 'RETOUR_MATERIEL'     THEN 1
        WHEN p_statut_actuel = 'LIVRE'              AND p_statut_cible = 'TERMINEE'            THEN 1
        WHEN p_statut_actuel = 'RETOUR_MATERIEL'    AND p_statut_cible = 'TERMINEE'            THEN 1
        ELSE 0
    END;
END$$

-- ── Procédure : créer un compte utilisateur ou employé ───────────────────────
CREATE PROCEDURE sp_creer_compte(
    IN p_nom      VARCHAR(80),
    IN p_prenom   VARCHAR(80),
    IN p_mail     VARCHAR(255),
    IN p_gsm      VARCHAR(20),
    IN p_adresse  TEXT,
    IN p_mdp_hash VARCHAR(255),
    IN p_role     ENUM('UTILISATEUR','EMPLOYE','ADMINISTRATEUR')
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF EXISTS (SELECT 1 FROM utilisateur WHERE mail = p_mail) THEN
        SIGNAL SQLSTATE '45001'
            SET MESSAGE_TEXT = 'Cette adresse email est déjà utilisée.';
    END IF;

    IF p_role = 'ADMINISTRATEUR' THEN
        SIGNAL SQLSTATE '45002'
            SET MESSAGE_TEXT = 'Création d''un compte ADMINISTRATEUR interdite via cette procédure.';
    END IF;

    INSERT INTO utilisateur (nom, prenom, mail, gsm, adresse, mdp_hash, role)
    VALUES (p_nom, p_prenom, p_mail, p_gsm, p_adresse, p_mdp_hash, p_role);
END$$

-- ── Procédure : désactiver un compte (soft delete) ───────────────────────────
CREATE PROCEDURE sp_desactiver_compte(
    IN p_id_utilisateur INT
)
BEGIN
    DECLARE v_role VARCHAR(20);
    SELECT role INTO v_role FROM utilisateur WHERE id_utilisateur = p_id_utilisateur;

    IF v_role = 'ADMINISTRATEUR' THEN
        SIGNAL SQLSTATE '45003'
            SET MESSAGE_TEXT = 'Le compte administrateur ne peut pas être désactivé.';
    END IF;

    UPDATE utilisateur SET actif = 0 WHERE id_utilisateur = p_id_utilisateur;

    -- Invalider toutes les sessions actives
    DELETE FROM session WHERE id_utilisateur = p_id_utilisateur;
END$$

DELIMITER ;
