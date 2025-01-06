<?php
session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Random Board </title>
</head>
<body>
<form method="post">
    <label> start row: </label> <input type="number" name="startrow" min="1" max="6" required> <br>
    <label> start column: </label> <input type="number" name="startcol" min="1" max="6" required> <br>
    <label> end row: </label> <input type="number" name="endrow" min="1" max="6" required> <br>
    <label> end column: </label> <input type="number" name="endcol" min="1" max="6" required> <br>
    <button type="submit" name="check"> review move </button>
    <button type="submit" name="reset"> restart game </button>
</form>

<?php

function generate_combinations() {
    $digits = [1, 2, 3, 4, 5, 6];
    $colors = ["red", "blue", "green", "yellow", "purple", "orange"];
    $combinations = [];

    foreach ($digits as $digit) {
        foreach ($colors as $color) {
            $combinations[] = ["digit" => $digit, "color" => $color];
        }
    }

    return $combinations;
}

function create_board($combinations) {
    $indexes = range(0, count($combinations) - 1);
    shuffle($indexes);

    $grid = [];
    for ($i = 0; $i < 6; $i++) {
        $grid[] = array_slice($indexes, $i * 6, 6);
    }

    return $grid;
}

function display_board($grid, $combinations) {
    echo "<table border='6' style='text-align: center; border-color:blue; backgroundcolor: black; text-color: blue'>";
    foreach ($grid as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            $combination = $combinations[$cell];
            echo "<td>{$combination['digit']} {$combination['color']}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

function check_combination($start, $end, $combinations) {
    $start_comb = $combinations[$start];
    $end_comb = $combinations[$end];

    return $start_comb['digit'] == $end_comb['digit'] || $start_comb['color'] == $end_comb['color'];
}

function check_move($startrow, $startcol, $endrow, $endcol) {
    return $startrow == $endrow || $startcol == $endcol;
}

if (!isset($_SESSION['grid'])) {
    $_SESSION['combinations'] = generate_combinations();
    $_SESSION['grid'] = create_board($_SESSION['combinations']);
}

$combinations = $_SESSION['combinations'];
$grid = $_SESSION['grid'];

$startrow = $startcol = $endrow = $endcol = null;
$start = $end = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset'])) {
        $_SESSION['combinations'] = generate_combinations();
        $_SESSION['grid'] = create_board($_SESSION['combinations']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['check'])) {
        $startrow = (int)$_POST['startrow'] - 1; 
        $startcol = (int)$_POST['startcol'] - 1;
        $endrow = (int)$_POST['endrow'] - 1;
        $endcol = (int)$_POST['endcol'] - 1;

        if (!isset($grid[$startrow][$startcol]) || !isset($grid[$endrow][$endcol])) {
            echo "<p>Move out of range.</p>";
        } else {
            $start = $grid[$startrow][$startcol];
            $end = $grid[$endrow][$endcol];

            if (check_move($startrow, $startcol, $endrow, $endcol)) {
                if (check_combination($start, $end, $combinations)) {
                    echo "<p>Valid Move.</p>";
                } else {
                    echo "<p>Invalid Move.</p>";
                }
            } else {
                echo "<p>Move not allowed.</p>";
            }
        }
    }
}

if ($start !== null && $end !== null) {
    echo "<p>Start Row: {$startrow}, Start Column: {$startcol}</p>";
    echo "<p>End Row: {$endrow}, End Column: {$endcol}</p>";
    echo "<p>Start Combination: Digit: {$combinations[$start]['digit']} - Color: {$combinations[$start]['color']}</p>";
    echo "<p>End Combination: Digit: {$combinations[$end]['digit']} - Color: {$combinations[$end]['color']}</p>";
}

display_board($grid, $combinations);
?>
<?php
ob_end_flush();
?>
AEV52
