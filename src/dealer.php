<?php
// Creates empty array containing dealer hand, score, wins 
// and whether or not the dealer is bust

class Dealer{

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