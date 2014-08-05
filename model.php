<?php

function getProductCategories($categoryName) {
    $conn = testdbConn();
    $sql = 'SELECT * FROM categories ORDER BY categoryID';
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $ex) {
        $errormessage = 'Sorry, there was an error with the database.';
    }
    if (is_array($categories)) {
        return $categories;
    } else {
        return FALSE;
    }
}

function getContent($id) {
    $conn = testdbConn();
    $sql = "SELECT c.categoryID,c.categoryName,i.url,i.alt 
FROM categories c INNER JOIN images i 
ON c.categoryID = i.categoryID
WHERE categoryID=:id";
    try {
        $stmt = $conn->prepare($sql);
        $stmt ->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $content = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $ex) {
        $errormessage = 'Sorry, there was an error with the database.';
    }
    if (is_array($content)) {
        return $content;
    } else {
        return FALSE;
    }
}

function getHomeContent() {
    $conn = testdbConn();
    $sql = "SELECT c.categoryID,c.categoryName,i.url,i.alt 
FROM categories c INNER JOIN images i 
ON c.categoryID = i.categoryID
WHERE i.main = 'Y'";
    try {
        $stmt = $conn->prepare($sql);      
        $stmt->execute();
        $content = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $ex) {
        $errormessage = 'Sorry, there was an error with the database.';
    }
    if (is_array($content)) {
        return $content;
    } else {
        return FALSE;
    }
}

function addCategory($catname) {
    $conn = testdbConn();
    $sql = 'INSERT INTO categories(categoryName)VALUES(:categoryName)';
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':categoryName', $catname, PDO::PARAM_STR);
        $result = $stmt->execute();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $message = 'PDO error in model';
    }
    if ($result) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function testdbConn() {
    $server = 'localhost';
    $username = 'thenecro_iClient';
    $password = '7XyLrtC96HQT2mpq';
    $database = 'thenecro_testdb';
    $dsn = "mysql:host=$server; dbname=$database";
    try {
        $testdbConn = new PDO($dsn, $username, $password);
    } catch (PDOException $exc) {
        echo 'Sorry the connection could not be built';
    }

    if (is_object($testdbConn)) {
        return $testdbConn;
    } else {
        echo 'It failed';
    }
}

function updateInfo($price, $productID) {
    $conn = testdbConn();
    $sql = 'UPDATE products SET listPrice=:price WHERE productID=:productID';
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":productID", $productID);
        $stmt->bindParam(":price", $price);
        $stmt->execute();
//        $products=$stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $ex) {
        $errormessage = "sorry there was an error with the database";
    }
}

function getProductPrice() {
    $conn = testdbConn();
    $sql = 'SELECT listPrice, productID, productName FROM products  ORDER BY productID';
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $LP = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $ex) {
        $errormessage = 'Sorry, there was an error with the database.';
    }
    if (is_array($LP)) {
        return $LP;
    } else {
        return FALSE;
    }
}

function createUser($fName, $lName, $DOB, $gender, $passwordHashed) {
    $conn = testdbConn();
    global $link;
    $link->beginTransaction();
// A flag to determine if the transaction is working
    $flag = TRUE;
    try {
// Insert the first 3 variables using a prepared statement
        $sql = "INSERT INTO thenecro_tesdb.people 
         (first_name, last_name, gender, DOb) 
         VALUES (:first, :last, :dob :gender)";
        /*
         *      $fName=$_POST['fName'];
          $lName=$_POST['lName'];
          $email=$_POST['email'];
          $pswd=$_POST['password'];
          $DOB=$_POST['dob'];
          $gender=$_POST['gender'];
         */
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':first', $fname, PDO::PARAM_STR);
        $stmt->bindValue(':last', $lname, PDO::PARAM_STR);
        $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindValue(':DOB', $DOB, PDO::PARAM_STR);
        $stmt->execute();
// Determine if the insert worked
// by getting the primary key created by the insert
        $userid = $link->lastInsertId();
        $stmt->closeCursor();
    } catch (Exception $e) {
        return 0;
    }
    if ($userid < 1) {
        $flag = FALSE;
    }
    if ($flag) {
        try {
// Write the other variables and the primary 
// key from the first table to the second table
            $sql = "INSERT INTO 
         thenecro_testdb.credentials(cred_email, cred_password) 
         VALUES (:cred_email, :cred_password)";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $passwordHashed, PDO::PARAM_STR);
            $stmt->bindValue(':peopleid', $userid, PDO::PARAM_INT);
            $stmt->execute();
            $rowcount = $stmt->rowCount(); //How many rows were added
            $stmt->closeCursor();
        } catch (PDOException $e) {
// set flag to false indicating the insert 
// could not be completed
            $flag = FALSE;
        }
    }
    if ($rowcount != 1) {
        $flag = FALSE;
    }
    if ($flag) {
        $link->commit();
        return 1;
    } else {
        $link->rollback();
        return 0;
    }
}

function getCategoryItem($id) {
    $conn = testdbConn();
    try {
        //filter record set return
        $sql = 'SELECT categoryID, categoryName FROM categories WHERE categoryID=:id';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $exc) {
        echo $exc->getTraceAsString();
    }
    if (is_array($data)) {
        return $data;
    } else {
        return FALSE;
    }
}

function updateCategory($catname, $catId) {
    $conn = testdbConn();
    try {
        $sql = 'UPDATE categories SET categoryName=:catname WHERE categoryID = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $catID, PDO::PARAM_INT);
        $stmt->bindValue(':catname', $catname, PDO::PARAM_STR);
        $stmt->execute();
        $updateResult = $stmt->rowCount();
        $stmt->closeCursor();
    } catch (PDOException $exc) {
        echo $exc->getTraceAsString();
    }
    if ($updateResult) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//