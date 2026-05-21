# Vite & Gourmand

Manuel d'utilisation Application web de commande de menus traiteur

Version 1.0 — 2024

Titre Professionnel Développeur Web et Web Mobile

### 1. Présentation de l'application

Vite & Gourmand est une application web de commande de menus traiteur développée pour l'entreprise
bordelaise du même nom, fondée par Julie et José. Elle permet aux visiteurs de consulter les menus
disponibles, de passer commande en ligne et aux équipes (employés et administrateur) de gérer
l'ensemble des commandes, menus et plats.

URL de l'application : https://[À COMPLÉTER].fly.dev

Fonctionnalités principales

• Consultation des menus avec filtres dynamiques (thème, régime, prix, personnes)

• Commande en ligne avec calcul automatique des frais de livraison et réductions

• Espace utilisateur : suivi des commandes, modification, annulation, dépôt d'avis

• Espace employé : gestion des commandes, statuts, avis, menus, plats, horaires

• Espace administrateur : gestion des employés, statistiques MongoDB

• Envoi automatique de mails (confirmation, suivi, retour matériel, avis)

### 2. Accès et identifiants de test
Les identifiants ci-dessous permettent de tester les différents parcours de l'application. Le mot de passe
respecte les règles de sécurité : 10 caractères minimum, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère
spécial.

Rôle            Email               Mot de passe

Administrateur  jose@vitegourmand.fr    [Admin@1234]

Employé         bruno@exemple.fr        [Employe@1234]

Utilisateur     henri.dunand@exemple.fr [Terminator@5678]


Note : Le compte Administrateur ne peut pas être créé depuis l'application. Il doit être créé directement en base de
données.

### 3. Parcours Visiteur (non connecté)
    
    3.1 Consulter les menus
        
        1. Accéder à la page d'accueil via l'URL de l'application
        
        2. Cliquer sur "Menus" dans la barre de navigation
        
        3. Utiliser les filtres (thème, régime, prix, nombre de personnes) pour affiner la recherche
        
        4. Cliquer sur "Voir le détail" pour consulter un menu complet (plats, allergènes, conditions)
    
    3.2 Créer un compte
        
        1. Cliquer sur "Inscription" dans la barre de navigation
        
        2. Remplir le formulaire : nom, prénom, email, GSM, adresse, mot de passe
        
        3. Valider — un mail de bienvenue est envoyé automatiquement
    
    3.3 Contacter l'entreprise
        
        1. Cliquer sur "Contact" dans la barre de navigation
        
        2. Remplir le formulaire : sujet, message, adresse email
        
        3. Valider — le message est envoyé par mail à l'équipe Vite & Gourmand
        

### 4. Parcours Utilisateur (connecté)
    
    4.1 Se connecter
        
        1. Cliquer sur "Connexion" dans la barre de navigation
        
        2. Saisir l'email et le mot de passe
        
        3. Cliquer sur "Se connecter"
    
    Identifiants :
        
        Rôle        Email                   Mot de passe
        
        Utilisateur henri.dunand@exemple.fr [Terminator@5678]
    
    4.2 Passer une commande
        
        1. Consulter les menus et cliquer sur "Voir le détail" d'un menu
        
        2. Cliquer sur "Commander ce menu"
        
        3. Vérifier les informations pré-remplies (nom, email, GSM)
        
        4. Choisir le nombre de personnes (min. selon le menu)
        
        5. Saisir l'adresse de livraison et la date/heure souhaitée
        
        6. Vérifier le récapitulatif du prix (réduction 10% si +5 personnes au minimum, frais livraison hors Bordeaux)
        
        7. Cliquer sur "Confirmer la commande" — un mail de confirmation est envoyé
    
    4.3 Suivre et gérer ses commandes
        
        1. Cliquer sur son prénom (menu déroulant) puis "Mon Profil"
        
        2. La liste des commandes est affichée avec leur statut
        
        3. Cliquer sur "Voir le détail" pour consulter le suivi complet
        
        4. Une commande EN ATTENTE peut être modifiée ou annulée
        
        5. Une fois la commande TERMINÉE, cliquer sur "Laisser un avis" (note 1-5 + commentaire)

### 5. Parcours Employé
    
    Identifiants :
    
    Rôle    Email            Mot de passe
    
    Employé bruno@exemple.fr [Employe@1234]

   
   5.1 Accéder au Dashboard
      
      Après connexion, la barre de navigation affiche "Dashboard" (fond vert). Le dashboard présente les
      statistiques des commandes en temps réel.
   
   5.2 Gérer les commandes
        
        1. Cliquer sur "Voir les commandes" ou "Gérer les commandes"
        
        2. Filtrer par statut ou rechercher un client
        
        3. Modifier le statut d'une commande via le menu déroulant + bouton 3
        
        4. Annuler une commande en précisant le mode de contact et le motif (obligatoires)
        
        5. Consulter l'historique complet d'une commande via le bouton "Historique"
            
            Statuts disponibles :
                
                • EN ATTENTE : Commande reçue, en attente de validation
                
                • ACCEPTE : Commande validée par l'équipe
                
                • EN PREPARATION : Commande en cours de préparation
                
                • EN COURS LIVRAISON : Commande en cours de livraison
                
                • LIVRE : Commande livrée au client
                
                • EN ATTENTE RETOUR MATERIEL : Matériel prêté — en attente de restitution (mail envoyé au client)
                
                • TERMINEE : Commande clôturée (mail envoyé pour laisser un avis)
                
                • ANNULEE : Commande annulée
    
    5.3 Gérer les avis
        
        1. Cliquer sur "Modérer les avis" depuis le dashboard
        
        2. Les avis en attente de validation sont listés
        
        3. Cliquer sur "Valider" pour publier l'avis sur la page d'accueil
        
        4. Cliquer sur "Refuser" pour rejeter l'avis
    
    5.4 Gérer les menus et plats
        
        1. Depuis le dashboard, cliquer sur "Gérer les menus" ou "Gérer les plats"
        
        2. Créer, modifier ou supprimer un menu/plat
        
        3. Associer des plats à un menu et des allergènes à chaque plat
    
    5.5 Gérer les horaires
        
        1. Depuis le dashboard, cliquer sur "Gérer les horaires"
        
        2. Cliquer sur "Modifier" pour un jour donné
        
        3. Cocher "Fermé ce jour" ou saisir les heures d'ouverture/fermeture
        
        4. Les horaires sont automatiquement affichés dans le pied de page du site

### 6. Parcours Administrateur
    
    Identifiants :
    
    Rôle           Email                Mot de passe
    
    Administrateur jose@vitegourmand.fr [Admin@1234]
    
    L'administrateur dispose de toutes les fonctionnalités de l'employé, plus les fonctionnalités suivantes :

    
    6.1 Gérer les employés
        
        1. Cliquer sur "Employés" dans la barre de navigation (fond rouge)
        
        2. Créer un compte employé en saisissant un email et un mot de passe
        
        3. L'employé reçoit un mail de notification (le mot de passe n'est pas communiqué par mail)
        
        4. Désactiver un compte employé en cas de départ de l'entreprise
    
    6.2 Consulter les statistiques
        
        1. Cliquer sur "Statistiques" dans la barre de navigation
        
        2. Consulter le graphique du nombre de commandes par menu
        
        3. Consulter le graphique du chiffre d'affaires par menu
        
        4. Les données proviennent de MongoDB et se mettent à jour à chaque commande
### 7. Règles métier importantes
        
        Nombre minimum de personnes
            
            Chaque menu a un nombre minimum de personnes. Il est impossible de commander en dessous de ce seuil.
        
        Réduction 10%
            
            Une réduction de 10% est automatiquement appliquée si le nombre de personnes commandé est supérieur ou égal
            au minimum + 5.
        
        Frais de livraison
            
            La livraison est gratuite pour toute adresse à Bordeaux. Hors Bordeaux : 5€ + 0,59€ par kilomètre parcouru.
        
        Annulation employé
            
            Un employé ne peut annuler une commande qu'après avoir contacté le client (par GSM ou mail). Le mode de
            contact et le motif sont obligatoires.
        
        Retour de matériel
            
            Si du matériel a été prêté, le client dispose de 10 jours ouvrés pour le restituer. 
            Passé ce délai, des frais de 600€ sont facturés (CGV).
        
        Modification de commande
            
            Un utilisateur peut modifier sa commande uniquement tant qu'elle est EN ATTENTE (avant acceptation par
            l'équipe). Le menu ne peut pas être changé.
        
        Dépôt d'avis
            
            Un avis ne peut être déposé que sur une commande TERMINÉE. Il est soumis à validation par un employé avant
            publication.
### 8. Sécurité et conformité
        
        • Mots de passe hashés avec bcrypt (Symfony Security)
        
        • Protection CSRF sur tous les formulaires
        
        • Contrôle d'accès par rôles (ROLE_USER, ROLE_EMPLOYE, ROLE_ADMIN)
        
        • Validation et assainissement des données côté serveur (PHP/Symfony)
        
        • Détection de code malveillant côté client (JavaScript)
        
        • Conformité RGPD : données collectées uniquement pour la gestion des commandes
        
        • Accessibilité RGAA : lien d'évitement, aria-labels, focus visible, contrastes
        
        • Compte administrateur non créable depuis l'application
    
    Pour toute question technique, contacter l'équipe de développement FastDev.

