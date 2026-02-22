<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT role,userName, passwd,status FROM users WHERE userName = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result && $result->num_rows > 0){
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['passwd'])){
            if($row['status'] == 'active'){
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $row["role"];
                switch ($row["role"]){
                    case "student":
                        header("Location: ../dashbords/student.html");
                        exit();
                    case "admin":
                        header("Location: ../dashbords/admin.php");
                        exit();
                    case "staff":
                        header("Location: ../dashbords/staff.html");
                        exit();
                }
            }else{
                header("Location: ../index.php?state=suspended");
            exit();
            }
        }else{
            header("Location: ../index.php?error=1");
            exit();
        }
    }else{
        header("Location: ../index.php?error=1");
        exit();
    }
}
?>
