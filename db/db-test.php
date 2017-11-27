<?php

    $db = new SQLite3('data.db');
    $results = $db->query('SELECT * FROM user');

    $resultArr = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
      $resultArr[] = $row;
    }    

    echo '<pre>';
    print_r($resultArr);
    echo '</pre>';


?>