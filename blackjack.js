// Functions are API calls to router.php which handles routing to the blackjack.php engine

// Function autoruns on HTML load. Begins new/first round
function start_round(){

    $("div#round-finish-menu").hide();

    $.ajax({
        url:'/src/router.php?request=start_round',
        type:'GET',
        dataType:"json",
        success: function(result){

            // Update UI with starting hand and scores
            playerUpdateHand(result.player.hand);
            dealerUpdateHand(result.dealer.hand);
            dealerUpdateTotal(result.dealer.score);
            playerUpdateTotal(result.player.score);

            // Evaluate for tie
            if (result.player.bust === "tie"){
                
                dealerShowHand();
                tie();
                playerUpdateWins(result.player.wins);
                dealerUpdateWins(result.dealer.wins);
            }

            // Evaluate for winning hand on draw (lucky!)
            if (result.player.bust === "blackjack"){
                
                dealerShowHand();
                playerWin();
                playerUpdateWins(result.player.wins);
            }

        },
        error: function(err){console.log(err);} // Handle HTTP error codes
    });
}

// Draw a card
function hit(){

    $.ajax({
        url:'/src/router.php?request=hit',
        type:'GET',
        dataType:"json",
        success: function(result){

            // Update UI with new hand and scores
            playerUpdateHand(result.player.hand);
            playerUpdateTotal(result.player.score);

            console.log('player: '+result.player.bust+' dealer:'+result.dealer.bust);
            // Evaluate for a bust, blackjack or tie
            switch (result.player.bust){
                case true:

                    dealerShowHand();
                    playerBust();
                    dealerUpdateWins(result.dealer.wins);

                break;

                case "blackjack": 

                    dealerShowHand();
                    playerWin();
                    playerUpdateWins(result.player.wins);

                break;

                case "tie":
                
                    dealerShowHand();
                    tie();
                    playerUpdateWins(result.player.wins);
                    dealerUpdateWins(result.dealer.wins);
                
                break;
            }
        },
        error: function(err){console.log(err);} // Handle HTTP error codes
    });
}

// Let dealer start turn
function hold(){

    dealerShowHand();

    $.ajax({
        url:'/src/router.php?request=hold',
        type:'GET',
        success: function(result){
            
            // jQuery-isms wasn't allowing a correct parse from the AJAX call
            // returned it as a string and parsed into object with a JSON parser instead.
            result = JSON.parse(result);

            if(result.dealer.bust === true){
                
                dealerBust();
                playerUpdateWins(result.player.wins);
            }
            if(result.dealer.bust === "blackjack" && result.player.bust != "blackjack"){

                dealerWin();
                dealerUpdateWins(result.dealer.wins);
            }
            if(result.player.score === result.dealer.score){

                tie();
                dealerUpdateWins(result.dealer.wins);
                playerUpdateWins(result.player.wins);
            }
            if(result.dealer.bust === false && result.player.score < result.dealer.score){

                dealerWin();
                dealerUpdateWins(result.dealer.wins);
            }
            if(result.dealer.bust === false && result.player.score > result.dealer.score){

                playerWin();
                playerUpdateWins(result.player.wins);
            }

            // Show dealer hand after draw
            dealerUpdateHand(result.dealer.hand);
            dealerUpdateTotal(result.dealer.score);
        },
        error: function(err){console.log(err);} // Handle HTTP error codes
    });
}

// Resets all scores and win/loss counters by destroying the PHP session cookie
function resetGame(){

    document.cookie = "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    location.reload();
}

// Alert player of bust and start new round
function playerBust(){

    $("h3#game-state").html("Result: You went bust!");
    $("div#round-finish-menu").show();
}

// Alert player of win and start new round
function playerWin(){

    $("h3#game-state").html("Result: You won!");
    $("div#round-finish-menu").show();
}

// Alert dealer of bust and start new round
function dealerBust(){

    $("h3#game-state").html("Result: Dealer went bust! You win!");
    $("div#round-finish-menu").show();
}

// Alert dealer of win and start new round
function dealerWin(){

    $("h3#game-state").html("Result: Dealer wins!");
    $("div#round-finish-menu").show();
}

// Alert player of a tie and start new round
function tie(){

    $("h3#game-state").html("It's a tie! What are the odds?");
    $("div#round-finish-menu").show();
}

// During a win, loss or hold, the dealer needs to reveal their hand
function dealerShowHand(){

    $.ajax({
        url:'/src/router.php?request=dealer_hand',
        type:'GET',
        dataType:"json",
        success: function(result){

            dealerUpdateHand(result.dealer.hand);
            dealerUpdateTotal(result.dealer.score);
        }
    });
}

// Update Dealer Hand in UI
function dealerUpdateHand(cards){

    $('ul#dealer-card-list').html("");

    $.each(cards, function(index, card){

        $('ul#dealer-card-list').append("<li>" + card.value + " of " + card.suite + "</li>");
    });
}
// Update Player Hand in UI
function playerUpdateHand(cards){

    $('ul#player-card-list').html("");

    $.each(cards, function(index, card){

        $('ul#player-card-list').append("<li>" + card.value + " of " + card.suite + "</li>");
    });
}
// Update Dealer total card value in UI
function dealerUpdateTotal(score){

    $('p#dealer-card-total').html("Total: "+score);
}
// Update Player total card value in UI
function playerUpdateTotal(score){

    $('p#player-card-total').html("Total: "+score);
}
// Update Dealer win count in UI
function dealerUpdateWins(count){

    $('p#dealer-wins').html("Win: " + count)
}
// Update Player win count in UI
function playerUpdateWins(count){

    $('p#player-wins').html("Win: " + count)

}