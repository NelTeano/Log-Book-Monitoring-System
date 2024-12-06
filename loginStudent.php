<?php
session_start();
require 'dbconn1.php';

// For debugging
error_log("Action: " . $_REQUEST['action']);
error_log("Username: " . $_REQUEST['username']);
error_log("Password: " . $_REQUEST['password']);

class LoginStudent {

    public function checkUser($username) {
        error_reporting(E_ERROR);
        $config = new Config();

        $response = array();

        $conn = $config->conn;

        if ($conn->connect_error) {
            return $conn->connect_error;
        } else {
            $query = 'SELECT username from users where username = ?';

            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param('s', $username);

                if ($stmt->execute()) {
                    $stmt->bind_result($username);
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        return 'USER_FOUND';
                    } else {
                        return 'USER_NOT_FOUND';
                    }

                    $stmt->close();
                } else {
                    return $stmt->error;
                }
            } else {
                return $conn->error;
            }
            $conn->close();
        }
    }

    public function getHashPassword($username) {
        error_reporting(E_ERROR);
        $config = new Config();

        $response = array();

        $conn = $config->conn;

        if ($conn->connect_error) {
            return $conn->connect_error;
        } else {
            $query = 'SELECT password from users where username = ?';

            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param('s', $username);

                if ($stmt->execute()) {
                    $stmt->bind_result($password);
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        while ($stmt->fetch()) {
                            return $password;
                        }
                    }
                    $stmt->close();
                } else {
                    return $stmt->error;
                }
            } else {
                return $conn->error;
            }
            $conn->close();
        }
    }

    public function verifyInput($password, $hashpassword) {
        return password_verify($password, $hashpassword); // Using password_verify for better security
    }

    public function doLogin($username, $password) {
        $app = new LoginStudent();
        $response = array();

        $checkUser = $app->checkUser($username); 

        if($checkUser == 'USER_FOUND') {
            $hashpassword = $app->getHashPassword($username);
            $verifyPassword = $app->verifyInput($password, $hashpassword);

            if($verifyPassword == 1) {
                $response['isSuccess'] = true;
                $response['msg'] = 'Login Successful';
            } else {
                $response['isSuccess'] = false;
                $response['msg'] = 'Invalid Login Credentials';
            }
        } else if($checkUser == 'USER_NOT_FOUND') {
            $response['isSuccess'] = false;
            $response['msg'] = 'User not found';
        } else {
            $response['isSuccess'] = false;
            $response['msg'] = $checkUser;
        }

        return json_encode($response);
    }
}

$app = new LoginStudent();

if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'isLogin') {
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        $response = $app->doLogin($username, $password);
        $decode_response = json_decode($response);

        if ($decode_response->isSuccess) {
            $_SESSION['username'] = $username;
            header('Location: main_page.php');
            exit();
        } else {
            $_SESSION['login_error'] = $decode_response->msg;
            header('Location: login.html');
            exit();
        }
    }
} else {
    echo 'ERROR: No direct access';
}
?>
