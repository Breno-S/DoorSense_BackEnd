<?php

function answer($data) {
    header("Content-Type: application/json");
    echo json_encode($data);
}

?>
