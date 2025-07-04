<?php
// Load JSON data
$json_data = file_get_contents("data1.json");
$faq = json_decode($json_data, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_message = strtolower(trim($_POST["message"]));

    $best_match = null;
    $best_distance = PHP_INT_MAX;
    $best_answer = "I am not trained for that question.";

    // Loop through each category in JSON
    foreach ($faq as $category => $questions) {
        foreach ($questions as $item) {
            if (!isset($item["question"]) || !isset($item["answer"])) {
                continue; // Skip if key is missing
            }

            $question = strtolower($item["question"]);
            $answer = $item["answer"];
            $lev_distance = levenshtein($user_message, $question);

            // Check for exact match
            if ($user_message === $question) {
                echo $answer;
                exit;
            }

            // Check for closest match using Levenshtein distance
            if ($lev_distance < $best_distance) {
                $best_distance = $lev_distance;
                $best_match = $question;
                $best_answer = $answer;
            }
        }
    }

    // If a close match is found (within 3 character differences), suggest it
    if ($best_distance <= 3) {
        echo "Did you mean: '$best_match'? \n\n" . $best_answer;
    } else {
        echo $best_answer;
    }
}
?>
