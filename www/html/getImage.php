<?php

require 'config/db.php';
require_once 'config/define.php';

if (isset($_GET['id']))
{
    $id = $_GET['id'];

    if($id < 1)
        exit(0);

    $sql = "SELECT * FROM images WHERE id=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        exit(0);
    $stmt->bind_param('d', $id);

    if(!$stmt->execute())
        exit(0);
    $result = $stmt->get_result();
    if($result->num_rows < 1)
        exit(0);
    $images = $result->fetch_assoc();

    $data = array();
    $data['imageId'] = $images['id'];
    $data['image'] = IMAGE_DIRECTORY . $images['fileName'];
    $data['userId'] = $images['userId'];
    $data['uploadedOn'] = $images['uploadedOn'];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}

?>
