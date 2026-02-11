<?php
$number = 90000;  // Example number, you can set any value

$random_subtract = rand(0, 500);

if (($number - $random_subtract) > 0) {
    $result = $number - $random_subtract;
    echo "Original Number: " . $number . "\n";
    echo "First Number: " . $result;
     $data[] = [[0],[$result]];
     $arrayData = [
        "c2array" => true,
        "size" => [2, 1, 1],
        "data" => $data
    ];
    $arrayData = json_encode($arrayData);
} else {
     $arrayData = 'scoreArray":"'.$number.'"';
    }
echo "\nArray: $arrayData";
?>
