<?php

class Api {

    public $action;
    public $pdo;

    function __construct($pdo) {

        $this->pdo = $pdo;
    }

    function sanitize($var) {
        $var = htmlspecialchars(strip_tags(trim($var)));
        return $var;
    }

    // USER PART

    function usersList() {

        $q = "select * from users order by id";
        $w = mysqli_query($this->pdo, $q);      
        
        $tab = array();
        if (mysqli_num_rows($w) > 0) {
            while ($r = mysqli_fetch_assoc($w)) {
                $tab[] = $r;
            }
        }

        echo json_encode($tab);

    }

    function usersAdd() {

        $result = array(); 

        $slug = '';
        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }


        if(!isset($firstname) || $firstname=='' || !isset($lastname) || $lastname=='' || !isset($email) || $email=='') {

            $result['err'] = 1;
            $result['message'] = 'Brak danych o uzytkowniku';

        } else if (!$this->emailValidation($email)) {

            $result['err'] = 1;
            $result['message'] = 'Błędny adres email';
            
        } else {

            $userCheck = $this->usersEmailExists($email);

            if($userCheck == 0) {
                $sql = "INSERT INTO users (email, firstname, lastname, slug) VALUES ('".$email."','".$firstname."', '".$lastname."', '".$slug."')";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $result['ok'] = 1;
            } else {
                $result['err'] = 1;
                $result['message'] = 'Email w bazie';
            }

        }

        echo json_encode($result);

    }

    function usersEmailExists($email) {
        
        $result = 0;
        $q = "select * from users where email = '".$email."'";
        $w = mysqli_query($this->pdo, $q);      

        if (mysqli_num_rows($w) > 0) {
           $result = 1;
        }

        return $result;

    }

    function usersEdit() {

        $result = array(); 

        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }


        if(!isset($firstname) || $firstname=='' || !isset($lastname) || $lastname=='' || !isset($email) || $email=='' || !isset($id) || $id=='') {

            $result['err'] = 1;
            $result['message'] = 'Brak danych o uzytkowniku';
        
        } else {
   
            $sql = "UPDATE users SET firstname = '".$firstname."', lastname = '".$lastname."', slug = '".$slug."' WHERE id = '".$id."' AND email = '".$email."' LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result['ok'] = 1;
            
        }

        echo json_encode($result);

    }

    function usersDel() {
        
        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }

       if(!isset($id) || $id=='' || $id==0) {

            $result['err'] = 1;
            $result['message'] = 'Brak danych o uzytkowniku';
        
        } else {

            $checkUser = $this->checkUser($id);

            if($checkUser==1) {
               
                $sql = "DELETE FROM users WHERE id = '".$id."' LIMIT 1";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                
                $ch = curl_init('http://strona-internetowa.pl/vip/api-sms.php');
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('id'=>$id)));
                $data_str = curl_exec($ch);
                curl_close($ch);
            
                $sql = "DELETE FROM users_books WHERE id_user = '".$id."'";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();

                $result['ok'] = 1;
            
            } else {

                $result['err'] = 1;
                $result['message'] = 'Nic nie zostało usuniete';

            }
            
        }

        echo json_encode($result);

    }

    function emailValidation($email) { 
        return (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) ? false : true; 
    } 

    // END USER PART

    // BOOKS PART

    function booksAdd() {

        $result = array(); 

        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }


        if(!isset($title) || $title=='' || !isset($description) || $description=='' || !isset($shortDescription) || $shortDescription=='') {

            $result['err'] = 1;
            $result['message'] = 'Brak danych o ksiazce';
        
        } else {

            $sql = "INSERT INTO books (`title`, `description`, `shortDescription`) VALUES ('".$title."','".$description."', '".$shortDescription."')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result['ok'] = 1;    

        }

        echo json_encode($result);

    }

    function booksEdit() {

        $result = array(); 

        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }


        if(!isset($id) || $id=='' || !isset($title) || $title=='' || !isset($description) || $description=='' || !isset($shortDescription) || $shortDescription=='') {

            $result['err'] = 1;
            $result['message'] = 'Brak danych o ksiazce';
        
        } else {
   
            $sql = "UPDATE books SET `title` = '".$title."', `description` = '".$description."', `shortDescription` = '".$shortDescription."' WHERE id = '".$id."' LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result['ok'] = 1;
            
        }

        echo json_encode($result);

    }

    function booksDel() {
        
        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }

       if(!isset($id) || $id=='' || $id==0) {

            $result['err'] = 1;
            $result['message'] = 'Brak danych o ksiazce';
        
        } else {
   
            $sql = "DELETE FROM books WHERE id = '".$id."' LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $sql = "DELETE FROM users_books WHERE id_book = '".$id."'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $result['ok'] = 1;
            
        }

        echo json_encode($result);

    }

    // END BOOKS PART

    function userBookAdd() {

        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }

        if(!isset($id_user) || $id_user=='' || $id_user==0 || !isset($id_book) || $id_book=='' || $id_book==0) {

            $result['err'] = 1;
            $result['message'] = 'Brak danych do przypisania';
        
        } else {

            $checkBook = $this->checkBook($id_book);
            $checkUser = $this->checkUser($id_user);
            $checkConn = $this->checkConn($id_user, $id_book);

            if($checkConn == 1 && $checkBook==1 && $checkUser==1) {
                $result['err'] = 1;
                $result['message'] = 'Przypisanie istnieje';
            }
            else if($checkConn == 0 && $checkBook==1 && $checkUser==1) {
                $sql = "INSERT INTO users_books (`id_user`, `id_book`) VALUES ('".$id_user."', '".$id_book."')";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $result['ok'] = 1;
            } else {
                $result['err'] = 1;
                $result['message'] = 'Brak ksiazki lub uzytkownika';
            }
            
        }

        echo json_encode($result);

    }

    function checkBook($id) {
        
        $result = 0;
        $q = "select * from books where id = '".$id."'";
        $w = mysqli_query($this->pdo, $q);      

        if (mysqli_num_rows($w) > 0) {
           $result = 1;
        }

        return $result;

    }

    function checkUser($id) {

        $result = 0;
        $q = "select * from users where id = '".$id."'";
        $w = mysqli_query($this->pdo, $q);      

        if (mysqli_num_rows($w) > 0) {
           $result = 1;
        }

        return $result;

    }

    function checkConn($id_user, $id_book) {

        $result = 0;
        $q = "select * from users_books where id_user = '".$id_user."' and id_book = '".$id_book."'";
        $w = mysqli_query($this->pdo, $q);      

        if (mysqli_num_rows($w) > 0) {
           $result = 1;
        }

        return $result;

    }

    function userBookList() {

        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }

        if(!isset($id_user) || $id_user=='' || $id_user==0) {

            $result['err'] = 1;
            $result['message'] = 'Brak danych o uzytkowniku';
        
        } else {


            $q = "SELECT * FROM users_books
            RIGHT JOIN books
                ON books.id = users_books.id_book
            WHERE users_books.id_user = '". $id_user."'";
            $w = mysqli_query($this->pdo, $q);      
            
            $tab = array();
            if (mysqli_num_rows($w) > 0) {
                while ($r = mysqli_fetch_assoc($w)) {
                    $tab[] = $r;
                }
            }

            if(!empty($tab)) $result = $tab;
            else {
                $result['err'] = 1;
                $result['message'] = 'Brak wynikow';
            }

        }

        echo json_encode($result);

    }

    function booksSearch() {

        foreach ($_POST as $Key => $Value) {
            $$Key = $this->sanitize($Value);
        }

        if(!isset($search)) {

            $result['err'] = 1;
            $result['message'] = 'Brak frazy';
        
        } else {


            $q = "SELECT * FROM books
                    WHERE title LIKE '%". $search."%'";
            $w = mysqli_query($this->pdo, $q);      
            
            $tab = array();
            if (mysqli_num_rows($w) > 0) {
                while ($r = mysqli_fetch_assoc($w)) {
                    $tab[] = $r;
                }
            }

            if(!empty($tab)) $result = $tab;
            else {
                $result['err'] = 1;
                $result['message'] = 'Brak wynikow';
            }

        }

        echo json_encode($result);

    }


}

?>