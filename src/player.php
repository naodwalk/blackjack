<?php
// Creates empty array containing player hand, score, wins 
// and whether or not the player is bust

class Player{

    public $hand;
    public $bust;
    public $score;
    public $wins;

    function __construct(){

        $this->hand   = [];
        $this->bust   = false;
        $this->score  = 0;
        $this->wins   = 0;
    }
}
?>