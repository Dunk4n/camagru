<?php

require 'config/db.php';
require_once 'config/define.php';

if (isset($_GET['id']))
{
    $id = $_GET['id'];
    if(!is_numeric($id))
        exit(0);
    $data = array();

    if($id < 0)
    {
        $data['success'] = 0;
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit(0);
    }

    $sql = "SELECT * FROM images ORDER BY uploadedOn LIMIT ?, 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
    {
        $data['success'] = 0;
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit(0);
    }
    $stmt->bind_param('i', $id);

    if(!$stmt->execute())
    {
        $data['success'] = 2;
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit(0);
    }
    $result = $stmt->get_result();
    if($result->num_rows < 1)
    {
        $data['success'] = 3;
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit(0);
    }
    $images = $result->fetch_assoc();

    $data['success'] = 1;
    $data['imageId'] = $images['id'];
    $data['image'] = IMAGE_DIRECTORY . $images['fileName'];
    $data['userId'] = $images['userId'];
    $data['uploadedOn'] = $images['uploadedOn'];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}

?>
