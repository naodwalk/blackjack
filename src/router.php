<?php
// Blackjack app
require("blackjack.php");

// Check for existing PHP session
validate_session();

// Check for existing game
if (!isset($_SESSION['blackjack'])){
    
    $blackjack = new Blackjack();
    
    $_SESSION['blackjack'] = $blackjack;
}

// Validate the HTTP request and route accordingly
// GET request router
if (isset($_GET['request'])){

    $request = $_GET['request'];

    // Runs respective functions in blackjack.php dependency
    switch($request){

        // Begin new round
        case "start_round": 

             $_SESSION['blackjack']->start_round();
            
            // Dealer only reveals first card on deal
            $dealerHiddenCard = array(
                "suite" => "?",
                "value" => "?",
                "score" => 0,
            );

            // Replace dealer hand with hidden card 
            // But maintain real hand in session
            $dealerHand    = $_SESSION['blackjack']->dealer->hand;
            $dealerHand[0] = $dealerHiddenCard;
            
            // Hidden card isn't scored until player hold
            $currentHand = Score::total([$dealerHand[1]]);

            $result = array(
                "dealer" => array(
                    "hand"  => $dealerHand, 
                    "score" => $currentHand['totalScore'],
                    "bust"  => $_SESSION['blackjack']->dealer->bust,
                    "wins"  => $_SESSION['blackjack']->dealer->wins
                ),
                "player" => array(
                    "hand"  => $_SESSION['blackjack']->player->hand,
                    "score" => $_SESSION['blackjack']->player->score,
                    "bust"  => $_SESSION['blackjack']->player->bust,
                    "wins"  => $_SESSION['blackjack']->player->wins
                )
            );
            
            echo(json_encode($result));
        break;
        // Draw a card
        case "hit":

            $_SESSION['blackjack']->hit("player");

            // Custom array. Add wins to track between rounds
            // Added here to reduce number of API calls for UI
            $result = array(
                "player" => array( 
                    "hand"  => $_SESSION['blackjack']->player->hand,
                    "score" => $_SESSION['blackjack']->player->score,
                    "bust"  => $_SESSION['blackjack']->player->bust,
                    "wins"  => $_SESSION['blackjack']->player->wins
                ),
                "dealer" => array(
                    "wins"  => $_SESSION['blackjack']->dealer->wins
                )
            );

            echo(json_encode($result));
        break;
        // Player is done drawing. Pass turn to robot.
        case "hold":

            $_SESSION['blackjack']->hold();

            // Store final results in array
            $result = array(
                "player" => array( 
                    "hand"  => $_SESSION['blackjack']->player->hand,
                    "score" => $_SESSION['blackjack']->player->score,
                    "bust"  => $_SESSION['blackjack']->player->bust,
                    "wins"  => $_SESSION['blackjack']->player->wins
                ),
                "dealer" => array(
                    "hand"  => $_SESSION['blackjack']->dealer->hand,
                    "score" => $_SESSION['blackjack']->dealer->score,
                    "bust"  => $_SESSION['blackjack']->dealer->bust,
                    "wins"  => $_SESSION['blackjack']->dealer->wins
                )
            );

            echo(json_encode($result));
        break;
        // Reveals the full dealers hand
        case "dealer_hand":

            $result = array(
                "dealer" => array(
                    "hand"  => $_SESSION['blackjack']->dealer->hand,
                    "score" => $_SESSION['blackjack']->dealer->score
                )
            );

            echo(json_encode($result));
        break;
        // Error out invalid requests.
        default:
            error_malformed_reqeuest('Invalid GET request');
        break;
    }
    // Close PHP session and write data
    close_session();
}

// Start/Resume PHP session to store game state between HTTP calls
function validate_session(){

    if (session_status() === PHP_SESSION_NONE){

        // If a session existed, set ID from browser cookies
        if (isset($_COOKIE['PHPSESSID'])){

            session_id($_COOKIE['PHPSESSID']);
        }

        // Begin session. If no ID provided a new one is generated
        session_start();
        return(session_id());
    }

    // If session is already running, move on.
    return(true);
}

// Save session state for subsequent calls
function close_session(){

    // Save session state and gracefully close
    session_write_close();
}

// Function to return malformed request errors
function error_malformed_reqeuest($function) {

    trigger_error('Malformed request: '.$function);
    die();
}
?>