
-- ============================================================================
-- DONNÉES INITIALES (seed)
-- ============================================================================

-- Allergènes réglementaires EU (14 allergènes — Règlement 1169/2011)
INSERT INTO allergene (nom, code_eu) VALUES
    ('Gluten',                  'EU-01'),
    ('Crustacés',               'EU-02'),
    ('Oeufs',                   'EU-03'),
    ('Poissons',                'EU-04'),
    ('Arachides',               'EU-05'),
    ('Soja',                    'EU-06'),
    ('Lait',                    'EU-07'),
    ('Fruits à coque',          'EU-08'),
    ('Céleri',                  'EU-09'),
    ('Moutarde',                'EU-10'),
    ('Graines de sésame',       'EU-11'),
    ('Anhydride sulfureux',     'EU-12'),
    ('Lupin',                   'EU-13'),
    ('Mollusques',              'EU-14');

-- Entreprise (singleton)
INSERT INTO entreprise (nom, description, adresse, telephone, email, professionnalisme) VALUES
    ('Vite & Gourmand',
     'Entreprise de traiteur bordelaise fondée par Julie et José, forte de 25 années d''expérience dans la gastronomie régionale.',
     '12 rue des Chartrons, 33000 Bordeaux',
     '05 56 XX XX XX',
     'contact@vite-gourmand.fr',
     'Nous mettons notre savoir-faire et notre passion au service de vos événements pour vous offrir une expérience culinaire inoubliable.');

-- Horaires initiaux (lun-ven 9h-18h, sam 10h-16h, dim fermé)
INSERT INTO horaire (id_entreprise, jour_semaine, heure_ouverture, heure_fermeture, est_ferme) VALUES
    (1, 1, '09:00', '18:00', 0),
    (1, 2, '09:00', '18:00', 0),
    (1, 3, '09:00', '18:00', 0),
    (1, 4, '09:00', '18:00', 0),
    (1, 5, '09:00', '18:00', 0),
    (1, 6, '10:00', '16:00', 0),
    (1, 7,  NULL,    NULL,   1);

-- Compte administrateur José (mot de passe à remplacer par le hash bcrypt réel)
-- IMPORTANT : le hash ci-dessous est un exemple — remplacer avant déploiement
INSERT INTO utilisateur (nom, prenom, mail, mdp_hash, role, actif) VALUES
    ('Dupont', 'José',
     'jose@vite-gourmand.fr',
     '$2y$12$PLACEHOLDER_HASH_A_REMPLACER_AVANT_DEPLOIEMENT_xxxxxxxxxxx',
     'ADMINISTRATEUR', 1);

-- ============================================================================
-- FIN DU SCRIPT MPD
-- ============================================================================
