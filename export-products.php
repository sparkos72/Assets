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

$sql = "SELECT * FROM product";
$result = $conn->query($sql);
$resultCount = $result->num_rows;

echo $resultCount . ' records found' . PHP_EOL;

// print_r($result->fetch_assoc());
// $resultCount = 0;

if ($resultCount > 0) {
    // create file
    $productFile = fopen("products.csv", "w") or die("Unable to open file!");
    echo 'file created' . PHP_EOL;

    $productCount = 0;

    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $row = cleanRow($row);

        // get data with no matching field & record as a note
        $note = 'ID: ' . '' . '|';
        $note .= 'Name: ' . '' . '|';
        $note .= 'Created: ' . $row['d1'] . '/' . $row['d2'] . '/' . $row['d3'] . ' ' . $row['d4'] . '|';
        $note .= 'Last Updated: ' . $row['d5'] . '/' . $row['d6'] . '/' . $row['d7'] . ' ' . $row['d8'];
        $note .= $row['m4'];

        // check stock level
        $inStock = ((int)$row['d5'] > 0);

        // build image src
        $imgSrc = '';

        // correct rarity
        if (strtolower($row['c8']) == 'none') {
            $row['c8'] = '';
        }

        // create description
        $desc = '<div class="product-details>';
        $desc .= ($row['m1'] != '') ? '<div class="brief-desc">' . $row['m1'] . '</div>' : '';
        $desc .= ($row['m2'] != '') ? '<div class="full-desc">' . $row['m2'] . '</div>' : '';
        $desc .= ($row['m3'] != '') ? '<div class="comments">' . $row['m3'] . '</div>' : '';
        $desc .= '<div class="product-categories>';
        $desc .= ($row['c2'] != '') ? '<div class="category factory">Factory: ' . $row['c2'] . '</div>' : '';
        $desc .= ($row['c3'] != '') ? '<div class="category designer">Designer: ' . $row['c3'] . '</div>' : '';
        $desc .= ($row['c4'] != '') ? '<div class="category type">Type: ' . $row['c4'] . '</div>' : '';
        $desc .= ($row['c5'] != '') ? '<div class="category period">Period: ' . $row['c5'] . '</div>' : '';
        $desc .= ($row['c6'] != '') ? '<div class="category condition">Condition: ' . $row['c6'] . '</div>' : '';
        $desc .= ($row['c7'] != '') ? '<div class="category origin">Origin: ' . $row['c7'] . '</div>' : '';
        $desc .= ($row['c8'] != '') ? '<div class="category rarity">Rarity: ' . $row['c8'] . '</div>' : '';
        $desc .= ($row['t9'] != '') ? '<div class="category size">Size: ' . $row['t9'] . '</div>' : '';
        $desc .= '</div>';
        $desc .= '</div>';

        // build data array
        $productContent['Handle'] = '';
        $productContent['Title'] = $row['title'];
        $productContent['Body-HTML'] = $desc;
        $productContent['Vendor'] = $row['c2']; //factory
        $productContent['Type'] = $row['c4'];
        $productContent['Tags'] = $row['c8'];
        $productContent['Published'] = '';
        $productContent['Option1-Name'] = 'Category';
        $productContent['Option1-Value'] = $row['c1'];
        $productContent['Option2-Name'] = 'Designer';
        $productContent['Option2-Value'] = $row['c3'];
        $productContent['Option3-Name'] = 'Origin : Period';
        $productContent['Option3-Value'] = $row['c7'] . ' : ' . $row['c5'];
        $productContent['Variant-SKU'] = '';
        $productContent['Variant-Grams'] = '';
        $productContent['Variant-Inventory-Tracker'] = '';
        $productContent['Variant-Inventory-Qty'] = '';
        $productContent['Variant-Inventory-Policy'] = '';
        $productContent['Variant-Fulfillment-Service'] = '';
        $productContent['Variant-Price'] = $row['n3'];
        $productContent['Variant-Compare-At-Price'] = '';
        $productContent['Variant-Requires-Shipping'] = $row['n4'];
        $productContent['Variant-Taxable'] = '';
        $productContent['Variant-Barcode'] = '';
        $productContent['Image-Src'] = $imgSrc;
        $productContent['Image-Alt-Text'] = $row['title'];
        $productContent['Gift-Card'] = '';
        $productContent['Google-Shopping-MPN'] = '';
        $productContent['Google-Shopping-Age Group'] = '';
        $productContent['Google-Shopping-Gender'] = '';
        $productContent['Google-Shopping-Google Product Category'] = '';
        $productContent['SEO-Title'] = $row['t1'];
        $productContent['SEO-Description'] = $row['t2'];
        $productContent['Google-Shopping-AdWords Grouping'] = '';
        $productContent['Google-Shopping-AdWords Labels'] = '';
        $productContent['Google-Shopping-Condition'] = $row['c6'];
        $productContent['Google-Shopping-Custom-Product'] = '';
        $productContent['Google-Shopping-Custom-Label-0'] = '';
        $productContent['Google-Shopping-Custom-Label-1'] = '';
        $productContent['Google-Shopping-Custom-Label-2'] = '';
        $productContent['Google-Shopping-Custom-Label-3'] = '';
        $productContent['Google-Shopping-Custom-Label-4'] = '';
        $productContent['Variant-Image'] = '';
        $productContent['Variant-Weight-Unit'] = '';

        $lineData = [];
        foreach ($productContent as $key => $value) {
            $lineData[] = $value;
        }
        fwrite($productFile, implode(',', $lineData) . "\n");
        $productCount++;

        // if ($productCount == 2) {
        //     print_r($productContent);
        //     break;
        // }
    }

    echo 'file closed: ' . $productCount . ' lines created' . PHP_EOL;
    fclose($productFile);
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
