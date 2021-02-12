<?php
// Handles card values for Blackjack

class Score{

    // Assign a score/value for each card in deck according to Blackjack rules
    static function cards($cards){

        foreach ($cards as &$card){

            $value = $card['value'];
            
            if ($value == 'Q' || $value == 'J' || $value == 'K'){
                $card['score'] = 10;
            }
            elseif ($value == 'A'){
                $card['score'] = 0; // Value will be adjusted later using 'ace' function
            }
            else
            {
                $card['score'] = (int)$card['value'];
            }
        }

        return ($cards);
    }

    // Calculate total score of player hand from deck array
    static function total($hand){

        // Value of all cards in hand
        $totalScore = 0;

        // Increment counter to determine position of Aces
        $i = -1;

        // Array of Ace indexes to assign value after all other cards are scored
        $aceCards = [];

        foreach ($hand as $card){
            
            $i++;

            if ($card['value'] != "A"){
                
                $totalScore = $totalScore + $card['score'];
            }
            else{

                array_push($aceCards, $i);
            }
        }

        // Re-evaluate Aces with each score to determine if value needs to be adjusted to prevent bust
        foreach ($aceCards as $ace){

            if ($totalScore <= 10){
            
                $hand[$ace]['score'] = 11;
                $totalScore = $totalScore + 11;
            }
            else {

                $hand[$ace]['score'] = 1;
                $totalScore = $totalScore + 1;
            }
        }
        
        $response = array(
            "totalScore" => $totalScore,
            "hand"       => $hand
        );

        return($response);
    }

    
}
?>