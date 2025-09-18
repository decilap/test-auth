<?php

namespace App\DataFixtures;

use App\Entity\Interest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Ulid;

class InterestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Données d'intérêts par catégorie, sans doublons
        $interestsData = [
            'Sport' => [
                'Football', 'Basket-ball', 'Course à pied', 'Natation', 'Yoga',
                'Randonnée', 'Escalade', 'Ski', 'Snowboard', 'Cyclisme'
            ],
            'Musique' => [
                'Festivals de musique', 'Concerts', 'Chanter', 'Jouer d’un instrument',
                'Écouter des vinyles'
            ],
            'Cinéma & Séries' => [
                'Films d’horreur', 'Comédies', 'Marvel', 'DC', 'Films de science-fiction',
                'Documentaires', 'Séries TV'
            ],
            'Art & Culture' => [
                'Musées', 'Théâtre', 'Opéra', 'Peinture', 'Sculpture', 'Photographie',
                'Visites de monuments historiques'
            ],
            'Jeux' => [
                'Jeux de société', 'Jeux vidéo', 'Échecs', 'Poker'
            ],
            'Cuisine & Gastronomie' => [
                'Cuisine', 'Pâtisserie', 'Dégustation de vin', 'Restaurants',
                'Food trucks', 'Brunch'
            ],
            'Lecture' => [
                'Romans', 'Bandes dessinées', 'Mangas', 'Poésie',
                'Livres de développement personnel'
            ],
            'Voyage' => [
                'Sac à dos', 'Road trips', 'Week-ends en ville', 'Plages',
                'Montagnes', 'Aventures', 'Camping'
            ],
            'Passe-temps créatifs' => [
                'Écriture', 'Dessin', 'Jardinage', 'Bricolage',
                'Couture', 'Poterie'
            ],
            'Vie nocturne' => [
                'Bars', 'Clubs', 'Pubs', 'Soirées entre amis'
            ],
            'Tranquillité' => [
                'Soirées tranquilles', 'Netflix & chill', 'Rester à la maison',
                'Se promener'
            ],
            'Nature & Plein air' => [
                'Plage', 'Pêche', 'Observation des oiseaux', 'Astronomie'
            ],
            'Animaux' => [
                'Chiens', 'Chats', 'Amoureux des animaux'
            ],
            'Bénévolat' => [
                'Travail caritatif', 'Aider les autres'
            ],
            'Environnement' => [
                'Écologie', 'Zéro déchet', 'Activisme environnemental'
            ],
            'Développement personnel' => [
                'Méditation', 'Pleine conscience', 'Apprendre une nouvelle langue',
                'Podcasts'
            ]
        ];

        // Parcourir chaque catégorie
        foreach ($interestsData as $category => $items) {
            foreach ($items as $name) {
                $interest = new Interest();
                $interest->setName($name);
                $interest->setCategory($category);

                $manager->persist($interest);
            }
        }
        $manager->flush();
        echo "✅ Intérêts chargés avec succès !\n";
    }
}