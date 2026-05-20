<?php
namespace App\DataFixtures;

use App\Entity\Allergene;
use App\Entity\ImageMenu;
use App\Entity\Menu;
use App\Entity\Plat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ── ALLERGÈNES : récupérer les existants, créer si absents ──────────
        $allergenes = [];
        $nomsAllergenes = [
            'Gluten', 'Crustacés', 'Œufs', 'Poissons', 'Arachides',
            'Soja', 'Lait', 'Fruits à coque', 'Céleri', 'Moutarde',
            'Graines de sésame', 'Anhydride sulfureux', 'Lupin', 'Mollusques'
        ];

        foreach ($nomsAllergenes as $nom) {
            $allergene = $manager->getRepository(Allergene::class)->findOneBy(['nom' => $nom]);
            if (!$allergene) {
                $allergene = new Allergene();
                $allergene->setNom($nom);
                $manager->persist($allergene);
            }
            $allergenes[$nom] = $allergene;
        }

        $manager->flush();

        // ── PLATS ────────────────────────────────────────────────────────────
        $platsData = [
            [
                'nom'         => 'Velouté de potiron',
                'type'        => 'entree',
                'description' => 'Velouté onctueux de potiron avec crème fraîche et graines de courge.',
                'allergenes'  => ['Lait'],
            ],
            [
                'nom'         => 'Salade de chèvre chaud',
                'type'        => 'entree',
                'description' => 'Salade verte, tomates cerises, noix et fromage de chèvre grillé.',
                'allergenes'  => ['Lait', 'Fruits à coque'],
            ],
            [
                'nom'         => 'Foie gras maison',
                'type'        => 'entree',
                'description' => 'Foie gras de canard fait maison, chutney de figues et pain brioché.',
                'allergenes'  => ['Gluten', 'Œufs', 'Lait'],
            ],
            [
                'nom'         => 'Carpaccio de Saint-Jacques',
                'type'        => 'entree',
                'description' => 'Saint-Jacques marinées au citron vert, huile d\'olive et herbes fraîches.',
                'allergenes'  => ['Mollusques'],
            ],
            [
                'nom'         => 'Bœuf bourguignon',
                'type'        => 'plat',
                'description' => 'Bœuf mijoté au vin rouge, carottes, champignons et lardons.',
                'allergenes'  => ['Céleri'],
            ],
            [
                'nom'         => 'Magret de canard aux cerises',
                'type'        => 'plat',
                'description' => 'Magret de canard rosé, sauce aux cerises et gratin dauphinois.',
                'allergenes'  => ['Lait'],
            ],
            [
                'nom'         => 'Saumon en croûte d\'herbes',
                'type'        => 'plat',
                'description' => 'Filet de saumon en croûte de persil et citron, purée de pommes de terre.',
                'allergenes'  => ['Poissons', 'Gluten', 'Lait'],
            ],
            [
                'nom'         => 'Risotto aux champignons',
                'type'        => 'plat',
                'description' => 'Risotto crémeux aux champignons sauvages et parmesan.',
                'allergenes'  => ['Lait'],
            ],
            [
                'nom'         => 'Tajine d\'agneau aux pruneaux',
                'type'        => 'plat',
                'description' => 'Agneau fondant aux pruneaux, amandes et épices orientales.',
                'allergenes'  => ['Fruits à coque'],
            ],
            [
                'nom'         => 'Bûche de Noël chocolat',
                'type'        => 'dessert',
                'description' => 'Bûche traditionnelle au chocolat noir, ganache et éclats de noisette.',
                'allergenes'  => ['Gluten', 'Œufs', 'Lait', 'Fruits à coque'],
            ],
            [
                'nom'         => 'Tarte Tatin',
                'type'        => 'dessert',
                'description' => 'Tarte aux pommes caramélisées, pâte feuilletée et crème fraîche.',
                'allergenes'  => ['Gluten', 'Œufs', 'Lait'],
            ],
            [
                'nom'         => 'Crème brûlée à la vanille',
                'type'        => 'dessert',
                'description' => 'Crème brûlée traditionnelle à la vanille de Madagascar.',
                'allergenes'  => ['Œufs', 'Lait'],
            ],
            [
                'nom'         => 'Mousse au chocolat',
                'type'        => 'dessert',
                'description' => 'Mousse au chocolat noir 70%, légère et aérienne.',
                'allergenes'  => ['Œufs', 'Lait'],
            ],
        ];

        $plats = [];
        foreach ($platsData as $data) {
            // Éviter les doublons si déjà chargé
            $plat = $manager->getRepository(Plat::class)->findOneBy(['nom' => $data['nom']]);
            if (!$plat) {
                $plat = new Plat();
                $plat->setNom($data['nom']);
                $plat->setType($data['type']);
                $plat->setDescription($data['description']);
                $plat->setActif(true);
                foreach ($data['allergenes'] as $nomAllergene) {
                    $plat->addAllergene($allergenes[$nomAllergene]);
                }
                $manager->persist($plat);
            }
            $plats[$data['nom']] = $plat;
        }

        $manager->flush();

        // ── MENUS ────────────────────────────────────────────────────────────
        $menusData = [
            [
                'titre'          => 'Menu Noël Prestige',
                'description'    => 'Un menu festif et raffiné pour célébrer Noël en famille ou entre amis.',
                'theme'          => 'NOEL',
                'regime'         => 'CLASSIQUE',
                'nbPersonnesMin' => 8,
                'prix'           => 320.00,
                'stock'          => 10,
                'conditions'     => 'Commande à effectuer minimum 7 jours avant la prestation. Conservation au réfrigérateur entre 0°C et 4°C.',
                'plats'          => ['Foie gras maison', 'Magret de canard aux cerises', 'Bûche de Noël chocolat'],
                'images'         => [
                    ['url' => 'https://images.unsplash.com/photo-1481931098730-318b6f776db0?w=800', 'alt' => 'Menu Noël Prestige', 'ordre' => 1],
                ],
            ],
            [
                'titre'          => 'Menu Classique Gourmand',
                'description'    => 'Un menu généreux et savoureux pour tous vos événements du quotidien.',
                'theme'          => 'CLASSIQUE',
                'regime'         => 'CLASSIQUE',
                'nbPersonnesMin' => 6,
                'prix'           => 180.00,
                'stock'          => 15,
                'conditions'     => 'Commande à effectuer minimum 3 jours avant la prestation.',
                'plats'          => ['Velouté de potiron', 'Bœuf bourguignon', 'Tarte Tatin'],
                'images'         => [
                    ['url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800', 'alt' => 'Menu Classique', 'ordre' => 1],
                ],
            ],
            [
                'titre'          => 'Menu Végétarien Printemps',
                'description'    => 'Un menu végétarien frais et coloré, idéal pour le printemps.',
                'theme'          => 'CLASSIQUE',
                'regime'         => 'VEGETARIEN',
                'nbPersonnesMin' => 4,
                'prix'           => 120.00,
                'stock'          => 20,
                'conditions'     => 'Commande à effectuer minimum 2 jours avant la prestation.',
                'plats'          => ['Salade de chèvre chaud', 'Risotto aux champignons', 'Crème brûlée à la vanille'],
                'images'         => [
                    ['url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800', 'alt' => 'Menu Végétarien', 'ordre' => 1],
                ],
            ],
            [
                'titre'          => 'Menu Pâques en Famille',
                'description'    => 'Célébrez Pâques avec ce menu convivial et savoureux pour toute la famille.',
                'theme'          => 'PAQUES',
                'regime'         => 'CLASSIQUE',
                'nbPersonnesMin' => 10,
                'prix'           => 280.00,
                'stock'          => 8,
                'conditions'     => 'Commande à effectuer minimum 5 jours avant la prestation. Livraison uniquement le dimanche matin.',
                'plats'          => ['Carpaccio de Saint-Jacques', 'Tajine d\'agneau aux pruneaux', 'Mousse au chocolat'],
                'images'         => [
                    ['url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800', 'alt' => 'Menu Pâques', 'ordre' => 1],
                ],
            ],
            [
                'titre'          => 'Menu Événement Mer',
                'description'    => 'Un menu axé sur les produits de la mer pour vos soirées événementielles.',
                'theme'          => 'EVENEMENT',
                'regime'         => 'CLASSIQUE',
                'nbPersonnesMin' => 12,
                'prix'           => 450.00,
                'stock'          => 5,
                'conditions'     => 'Commande à effectuer minimum 10 jours avant la prestation. Les produits de la mer sont livrés frais le jour J.',
                'plats'          => ['Carpaccio de Saint-Jacques', 'Saumon en croûte d\'herbes', 'Crème brûlée à la vanille'],
                'images'         => [
                    ['url' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800', 'alt' => 'Menu Mer', 'ordre' => 1],
                ],
            ],
        ];

        foreach ($menusData as $data) {
            // Éviter les doublons
            $menu = $manager->getRepository(Menu::class)->findOneBy(['titre' => $data['titre']]);
            if (!$menu) {
                $menu = new Menu();
                $menu->setTitre($data['titre']);
                $menu->setDescription($data['description']);
                $menu->setTheme($data['theme']);
                $menu->setRegime($data['regime']);
                $menu->setNbPersonnesMin($data['nbPersonnesMin']);
                $menu->setPrix((string)$data['prix']);
                $menu->setStock($data['stock']);
                $menu->setConditions($data['conditions']);
                $menu->setActif(true);
                $menu->setDateCreation(new \DateTimeImmutable());

                foreach ($data['plats'] as $nomPlat) {
                    if (isset($plats[$nomPlat])) {
                        $menu->addPlat($plats[$nomPlat]);
                    }
                }

                foreach ($data['images'] as $imgData) {
                    $image = new ImageMenu();
                    $image->setUrl($imgData['url']);
                    $image->setAlt($imgData['alt']);
                    $image->setOrdre($imgData['ordre']);
                    $image->setMenu($menu);
                    $manager->persist($image);
                }

                $manager->persist($menu);
            }
        }

        $manager->flush();
    }
}