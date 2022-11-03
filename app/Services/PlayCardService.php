<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PlayCardService
{
    // S: Spade, H: Heart, D: Diamond, C: Club
    const CARD_TYPES = ['S', 'H', 'D', 'C'];
    // A: 1, X: 10, J: 11, Q: 12, K: 13
    const CARD_LIST = [
        'A',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        'X',
        'J',
        'Q',
        'K'
    ];

    /**
     * Get distributed cards depend on number of player
     * @param int numberOfPlayer
     * @return array List distributed and remained cards
     */
    public function getDistributedCards(int $numberOfPlayer): array
    {
        $randomCards = $this->getRandomCards();
        // Get number of cards per player
        $numberOfCardPerPlayer = $this->getNumberOfCardPerPlayer(
            $numberOfPlayer,
            $randomCards->count()
        );

        $distributedCards = collect([]);
        $distributedCardTotal = 0;
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            $cardsPerPlayer = collect([]);
            if ($randomCards->count() > 0) {
                for ($j = 0; $j < $numberOfCardPerPlayer; $j++) {
                    // Get first item and remove
                    $cardsPerPlayer->push($randomCards->shift());
                    // Count distributed card
                    $distributedCardTotal++;
                }
            }

            $distributedCards->push([
                'person' => $i + 1,
                'cards' => $cardsPerPlayer
            ]);
        }

        return [
            'distributedCards' => $distributedCards,
            'remainedCards' => $randomCards,
            'distributedCardTotal' => $distributedCardTotal
        ];
    }

    /**
     * Get random cards
     */
    private function getRandomCards(): Collection
    {
        // Create deck 52 cards
        $cards = collect([]);
        foreach (self::CARD_TYPES as $cardType) {
            foreach (self::CARD_LIST as $card) {
                $cards->push($cardType . '-' . $card);
            }
        }

        // Generating random cards in deck before distributing to players
        return $cards->shuffle();
    }

    /**
     * Get number of card per player
     * @param int $numberOfPlayer
     * @param int $cardLength
     * @return int maximum number of card per player
     */
    private function getNumberOfCardPerPlayer(int $numberOfPlayer, int $cardLength): int
    {
        // Get max number of player <= card length
        $maxNumberOfPlayer = $numberOfPlayer;
        if ($numberOfPlayer >= $cardLength) {
            $maxNumberOfPlayer = $cardLength;
        }

        return floor($cardLength / $maxNumberOfPlayer);
    }
}
