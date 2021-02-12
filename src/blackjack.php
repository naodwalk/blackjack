<?php
// Blackjack main app

// Load dependencies
require("dealer.php"); // Extends score.php
require("player.php");
require("deck.php");

class Blackjack{

    // Instantiate object variables
    public  $dealer;
    public  $player;
    private $deck;

    function __construct(){ 

        // Instantiate objects to hold player decks and scores
        $this->dealer = new Dealer();
        $this->player = new Player();
    }

    // Initial function builds deck, shuffles and distributes
    // first two cards
    function deal(){

        $this->deck = new Deck();
        $this->deck->shuffle();

        $this->player->hand = $this->deck->draw(2);
        $this->dealer->hand = $this->deck->draw(2);
        
        // Score initial hands
        $currentHand = Score::total($this->dealer->hand);
        $this->dealer->score = $currentHand['totalScore'];
        $this->dealer->hand  = $currentHand['hand'];

        $currentHand = Score::total($this->player->hand);
        $this->player->score = $currentHand['totalScore'];
        $this->player->hand  = $currentHand['hand'];

        // Check for blackjack on draw
        $this->bust("player", $this->player->score);

        // Check for a double blackjack
        $this->tie();
    }
    
    // Draws card for selected player, adds to their deck array 
    // and calculates total points to evaulate for a bust
    function hit($target){

        // Run hit for player
        if ($target == "player"){

            // Draw a card and add to the player hand array
            array_push($this->player->hand, $this->deck->draw(1)[0]);

            // Count points for all cards in player deck
            $currentHand = Score::total($this->player->hand);
            $this->player->score = $currentHand['totalScore'];
            $this->player->hand  = $currentHand['hand'];
            
            // Evaluate for a bust
            $bust = $this->bust("player", $this->player->score);

            // On bust or blackjack declare winner
            // if both players blackjack declare tie

            if ($bust === true){

                $this->winner("dealer");
            }
            elseif ($bust === "blackjack"){

                $this->winner("player");
            };

            // Is tie
            $this->tie();
        }

        // Run hit for dealer
        elseif ($target == "dealer"){

            // Draw a card and add to the dealer hand array
            array_push($this->dealer->hand, $this->deck->draw(1)[0]);

            // Count points for all cards in dealer deck
            $currentHand = Score::total($this->dealer->hand);
            $this->dealer->score = $currentHand['totalScore'];
            $this->dealer->hand  = $currentHand['hand'];

            // Evaluate for a bust
            $bust = $this->bust("dealer", $this->dealer->score);

            // On bust or blackjack declare winner
            // if both players blackjack declare tie
            if ($bust === true){

                $this->winner("player");
            }
            elseif ($bust === "blackjack"){

                $this->winner("dealer");
            };
            
            // Is tie?
            $this->tie();
        }
    }

    // Player holds so robot begins to play
    function hold(){

        // Keep drawing until at least 17
        while ($this->dealer->score <= 17){
            trigger_error('not enough');
            $this->hit("dealer");
        } 

        // Decide to draw again if over 18 and under 20 w/ random decision
        if ($this->dealer->score >= 18 && $this->dealer->score <= 20 && rand(0, 1)){
            trigger_error('over draw');
            $this->hit("dealer");
        }

        // Assign the winner based on high score
        if ($this->dealer->score > $this->player->score && $this->dealer->bust === false){

            $this->winner("dealer");
        }

        if ($this->dealer->score < $this->player->score){

            $this->winner("player");
        }
    }

    // Begins a new round
    function start_round(){

        // Blank out values from previous round
        $this->player->hand   = [];
        $this->player->bust   = false;
        $this->player->score  = 0;

        $this->dealer->hand   = [];
        $this->dealer->bust   = false;
        $this->dealer->score  = 0;

        $this->deck           = [];

        // Deal new hand
        $this->deal();
    }

    // Determine if a player is bust (lost)
    function bust($target, $score){

        // Score over 21 is a loss
        // assign value accordingly
        if ($score > 21){

            switch ($target){

                case "player":
    
                    $this->player->bust = true;
                    
                    return(true);
                    break;
    
                case "dealer":
    
                    $this->dealer->bust = true;
                    
                    return(true);
                    break;
    
                default:
                    
                    trigger_error("Invalid player selection");
                break;
            }
        }
        // Score of 21 is a win
        // Assign value accordingly
        elseif ($score === 21){

            switch ($target){

                case "player":
    
                    $this->player->bust = "blackjack";
                    return("blackjack");
                    break;
    
                case "dealer":
    
                    $this->dealer->bust = "blackjack";
                    return("blackjack");
                    break;
    
                default:
                    
                    trigger_error("Invalid player selection");
                break;
            }
        }
        // Under 21 is no bust.
        //No winner or loser yet. Continue game
        else {
            return(false);
        }
    }

    // Declares a winner
    function winner($target){
        
        switch ($target){

            case "player":

                $this->player->wins++;
                break;

            case "dealer":

                $this->dealer->wins++;
                break;
        }
    }

    // Declares a tie and increments both player scores
    function tie(){

        if ($this->dealer->bust === "blackjack" && $this->player->bust === "blackjack"){

            $this->dealer->bust = "tie";
            $this->player->bust = "tie";

            $this->player->wins++;
            $this->dealer->wins++;

            return(true);
        }
        else{
            
            return(false);
        }
    }

}
?>