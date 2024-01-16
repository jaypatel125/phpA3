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
 * Chooses a random word from the wordlist file.
 *
 * @return string The randomly chosen word.
 */
function chooseRandomWord() {
    $words = file('wordlist.txt', FILE_IGNORE_NEW_LINES);
    $randomWord = trim($words[array_rand($words)]);
    return ($randomWord);
}

/**
 * Initializes the Hangman game session.
 *
 * @return void
 */
function initializeGame() {
    session_start();

    $_SESSION['secret'] = str_split(chooseRandomWord());
    $_SESSION['guesses'] = [];
    $_SESSION['strikes'] = 0;
    $_SESSION['status'] = "New game has started";

    // Initial game response
    $response = [
        'guesses' => '',
        'alphabet' => 'abcdefghijklmnopqrstuvwxyz',
        'secret' => str_repeat('- ', count($_SESSION['secret'])),
        'strikes' => $_SESSION['strikes'],
        'status' => $_SESSION['status'],
    ];

    echo json_encode($response);
}

/**
 * Plays a move in the Hangman game.
 *
 * @param string $letter The guessed letter.
 * @return void
 */ 
function playHangman($letter) {
    session_start();

     // Check if the player has already lost the game
        if ($_SESSION['strikes'] >= 6) {
            $_SESSION['status'] = "you have lost the game!";
            $_SESSION['strikes']++;
            $wordStatus = implode('', $_SESSION['secret']);
        } else {
            
            // Game is still in progress
            if (!in_array($letter, $_SESSION['guesses'])) {
                $_SESSION['guesses'][] = $letter;

                // Check if the guessed letter is not in the secret word
                if (strpos(implode('', $_SESSION['secret']), $letter) === false) {
                    $_SESSION['strikes']++;
                }
            }

            // Update the word status with correctly guessed letters
            $wordStatus = '';
            foreach ($_SESSION['secret'] as $char) {
                $wordStatus .= in_array($char, $_SESSION['guesses']) ? $char : '- ';
            }

            // Check if the player has guessed the entire word
            if ($wordStatus == implode('', $_SESSION['secret'])) {
                $_SESSION['status'] = "Congratulations! You won!";
            } else {
                $_SESSION['status'] = "Game in progress";
            }
        }

    // Prepare and output the game response
    $response = [
        'guesses' => implode('', $_SESSION['guesses']),
        'alphabet' => implode('', array_diff(range('a', 'z'), $_SESSION['guesses'])),
        'secret' => $wordStatus,
        'strikes' => $_SESSION['strikes'],
        'status' => $_SESSION['status'],
    ];

    echo json_encode($response);
}

// Retrieve parameters from the URL 
$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
$letter = isset($_GET['letter']) ? $_GET['letter'] : '';

// Check if the mode is 'reset' to initialize the game
if ($mode === 'reset') {
    initializeGame();
} else {
    playHangman($letter);
}
?>