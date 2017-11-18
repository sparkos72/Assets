<?php

$servername = "localhost";
$username = "root";
$password = "***";
$dbname = "teapotworld";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo PHP_EOL . 'connected to DB' . PHP_EOL;

$sql = "SELECT * FROM user";
$result = $conn->query($sql);
$resultCount = $result->num_rows;

echo $resultCount . ' records found' . PHP_EOL;

// print_r($result->fetch_assoc());
// $resultCount = 0;

if ($resultCount > 0) {
    // create file
    $customerFile = fopen("customers.csv", "w") or die("Unable to open file!");
    echo 'file created' . PHP_EOL;

    $customerCount = 0;

    // output data of each row
    while ($row = $result->fetch_assoc()) {
        if ($row['username'] == 'Unset') {
            continue;
        }

        $row = cleanRow($row);

        // correct name format
        $title = $row['title'];

        $nameArray = explode(' ', $row['fname']);
        $lastName = array_pop($nameArray);
        $firstName = implode(' ', $nameArray);

        // get data with no matching field & record as a note
        $note = 'ID: ' . $row['ref'] . '|';
        $note .= 'Name: ' . $title . ' ' . $firstName . ' ' . $lastName . '|';
        $note .= 'Created: ' . $row['d1'] . '/' . $row['d2'] . '/' . $row['d3'] . ' ' . $row['d4'] . '|';
        $note .= 'Last Updated: ' . $row['d5'] . '/' . $row['d6'] . '/' . $row['d7'] . ' ' . $row['d8'];
        $note .= $row['m4'];

        // look up xspend
        $totalSpent = 0;
        $totalOrders = 0;

        // build data array
        $customerContent = [];
        $customerContent['First-Name'] = $firstName;
        $customerContent['Last-Name'] = $lastName;
        $customerContent['Email'] = $row['email'];
        $customerContent['Company'] = '';
        $customerContent['Address1'] = $row['addr1'];
        $customerContent['Address2'] = $row['addr2'] . ($row['addr3'] != '') ? ' ' . $row['addr3'] : '';
        $customerContent['City'] = $row['town'];
        $customerContent['Province'] = $row['county'];
        $customerContent['Province-Code'] = '';
        $customerContent['Country'] = $row['country'];
        $customerContent['Country-Code'] = '';
        $customerContent['Zip'] = $row['postcode'];
        $customerContent['Phone'] = $row['tel1'];
        $customerContent['Accepts-Marketing'] = ($row['eList'] == 'On') ? 'yes' : 'no';
        $customerContent['Total-Spent'] = $totalSpent;
        $customerContent['Total-Orders'] = $totalOrders;
        $customerContent['Tags'] = '';
        $customerContent['Note'] = $note;
        $customerContent['Tax-Exempt'] = 'no';

        $lineData = [];
        foreach ($customerContent as $key => $value) {
            $lineData[] = $value;
        }
        fwrite($customerFile, implode(',', $lineData) . "\n");
        $customerCount++;

        // if ($customerCount == 2) {
        //     print_r($customerContent);
        //     break;
        // }
    }

    echo 'file closed: ' . $customerCount . ' lines created' . PHP_EOL;
    fclose($customerFile);
}
$conn->close();

echo 'disconnected from DB' . PHP_EOL . PHP_EOL;


function cleanRow($row)
{
    foreach ($row as $key => $value) {
        if ($value == 'Unset') {
            $row[$key] = '';
        }
    }

    return $row;
}
