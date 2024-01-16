<?php
// I Jay Patel, 000881881 certify that this material is my original work. No other person's work has been used without suitable acknowledgment and I have not made my work available to anyone else.
/**
* @author: Jay Patel
* @version: 202335.00
* @package COMP 10260 Assignment 3 
**/

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Initializes the Nim game session.
 *
 * @param int $count The initial count of stones in the game.
 * @return void
 */
function initializeGame($count) {
    session_start();
    
    $_SESSION['count'] = $count;
    $_SESSION['player_turn'] = true;
    $_SESSION['winner'] = "undetermined";
    
    // Initial game response
    $response = [
        'move' => 0,
        'stones' => $_SESSION['count'],
        'player' => "Computer",
        'winner' => "Game is started",
    ];
    
    echo json_encode($response);
}

/**
 * Plays a move in the Nim game.
 *
 * @param int $mode The mode of the game (0 for initialization).
 * @param int $difficulty The difficulty level of the game.
 * @param int $count The current count of stones in the game.
 * @param int|null $playerMove The move made by the player (null if computer's turn).
 * @return void
 */
function playNim($mode, $difficulty, $count, $playerMove) {
    if ($mode == 0) {
        initializeGame($count);
        return;
    }
    
    session_start();
    
    // Check if there are stones left to play
    if ($_SESSION['count'] > 0) {
        if ($_SESSION['player_turn'] && isset($playerMove)) {
            // Player's turn
            $_SESSION['count'] -= $playerMove;
            $player = "Player";
            $move = $playerMove;
        } elseif (!$_SESSION['player_turn']) {
            // Computer's turn
            if ($difficulty == 1) {
                // Get computer move based on strategy
                $move = getComputerMove($_SESSION['count']);
            } else {
                // Choose a random move
                $move = rand(1, 3);
            }
            $_SESSION['count'] -= $move;
            $player = "Computer";
        }
        
        $_SESSION['player_turn'] = !$_SESSION['player_turn'];
        
        // Ensure the stone count is non-negative
        $_SESSION['count'] = max(0, $_SESSION['count']);
    }
    
    // Check for a winner
    if ($_SESSION['count'] == 0 && isset($playerMove)) {
        if ($player === "Player") {
            $_SESSION['winner'] = "computer";
        } else {
            $_SESSION['winner'] = "player";
        }        
    }
    
    // Retrieve winner message and prepare response
    $winnerMessage = $_SESSION['winner'];

    $response = [
        'move' => ($move) ?? 0,
        'stones' => $_SESSION['count'],
        'player' => $player,
        'winner' => $winnerMessage,
    ];
    
    echo json_encode($response);
}

/**
 * Computes the computer's move based on the current count of stones.
 *
 * @param int $stones The current count of stones.
 * @return int The number of stones the computer chooses to remove.
 */
function getComputerMove($stones) {
    $remainder = $stones % 4;

    switch ($remainder) {
        case 3:
            return 2;
        case 2:
            return 1;
        case 1:
            // Choose a random move
            return rand(1, 3);
        case 0:
            return 3;
    }
}

// Retrieve parameters from the URL
$mode = 0;
if (isset($_GET['mode'])) {
    $mode = intval($_GET['mode']);
}

$difficulty = 0;
if (isset($_GET['difficulty'])) {
    $difficulty = intval($_GET['difficulty']);
}

$count = 20;
if (isset($_GET['count'])) {
    $count = intval($_GET['count']);
}

$playerMove = null;
if (isset($_GET['player_move'])) {
    $playerMove = intval($_GET['player_move']);
}

// Start the Nim game with the provided parameters
playNim($mode, $difficulty, $count, $playerMove);

?>
