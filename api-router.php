<?php
header("Content-Type: application/xml; charset=utf-8");

include('api-connect.php');
include('api-class.php');

$Api = new Api($mysqli);

$action = '';

if (isset($_GET['action'])) {
    $action = $Api->sanitize($_GET['action']);
} else if (isset($_POST['action'])) {
    $action = $Api->sanitize($_POST['action']);
} 

if ($action != '') {
    switch ($action) {
        case 'users-list': $Api->usersList();
            break;
        case 'users-add': $Api->usersAdd();
            break;
        case 'users-edit': $Api->usersEdit();
            break;
        case 'users-del': $Api->usersDel();
            break;

        case 'books-add': $Api->booksAdd();
            break;
        case 'books-edit': $Api->booksEdit();
            break;
        case 'books-del': $Api->booksDel();
            break;
        case 'books-search': $Api->booksSearch();
            break;

        case 'user-book-add': $Api->userBookAdd();
            break;
        case 'user-book-list': $Api->userBookList();
            break;
    }
}
?>