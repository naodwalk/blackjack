<?php
require("score.php");

// Emulates a standard card deck
class Deck extends Score{

    // Target array containing deck
    public $deck;

    // Constructs a new deck on instantiation. 
    // Edit JSON to add/remove suites/cards from deck (ie Joker)
    function __construct(){

        $suites = ["hearts", "spades", "clubs", "diamonds"];
        $values = ["A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K"];
        $deck  = [];

        // Builds an array of cards
        foreach ($suites as $suite){

            foreach ($values as $value){

                    $card = array(
                        "suite" => $suite,
                        "value" => $value
                    );

                    array_push($deck, $card);
            }
        }

        // Assigns point value to cards
        // Can be adjusted for different games in Scores class
        $this->deck = Score::cards($deck);
    }

    // Randomizes deck array. This is not done by default 
    // and needs to be called separately after instantiation
    public function shuffle(){

        $deckShuffled = shuffle($this->deck);

        return($this->deck);
    }

    // Cards drawn will be removed from the deck array
    // Supply [int] for number of cards
    public function draw($number){
        
        $cards = [];

        $i = 1; 
        while($i <= $number){

            $card  = array_pop($this->deck);
            array_push($cards, $card);

            $i++;
        }

        return($cards);
    }
};
?>