<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">       
  
  <title>VIP API</title>
  <meta name="description" content="default description">
  <meta name="keywords" content="default keywords">

  <script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>

  <!-- Custom scripts for all pages-->      

  <script>

    function usersList() {

        data = "action=users-list";

        $.ajax({
            type: "POST",
            data: data,
            url: "api-router.php",
            dataType: 'json',
            success: function(data) {	
                var dataString = JSON.stringify(data);
                $('#usersListContent').val(dataString);
            }
        }); 

    }

    $(document).ready(function() {
        
        $(".usersList").click(function(event) {  
            event.preventDefault();
            usersList();            
        });	

        $(".usersAdd").click(function(event) {  
            event.preventDefault();

            var data = "action=users-add&";
            var options = $('#usersAddOptions').val();
            
            if(options=='userA') {
                data += 'firstname=User&lastname=First&email=user@first.com';
            } else if(options=='userB') {
                data += 'firstname=User&lastname=Second&email=user@second.com&slug=sluging';
            } else if(options=='userC') {
                data += 'firstname=User&lastname=Third&email=user@third.com&slug=no';
            } else if(options=='userD') {
                data += 'firstname=User&lastname=Fourth&email=user@fourth&slug=';
            }

            $.ajax({
                type: "POST",
                data: data,
                url: "api-router.php",
                dataType: 'json',
                success: function(data) {	
                    var dataString = JSON.stringify(data);
                    $('#usersAddContent').val(dataString);

                    usersList();       
                }
            }); 
            
        });	

        $(".usersEdit").click(function(event) {  
            event.preventDefault();
            
            var slug = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < 8; i++ ) {
                slug += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            
            data = "action=users-edit&id=1&slug="+slug+"&firstname=user&lastname=default&email=user@default.com";

            $.ajax({
                type: "POST",
                data: data,
                url: "api-router.php",
                dataType: 'json',
                success: function(data) {	
                    var dataString = JSON.stringify(data);
                    $('#usersEditContent').val(dataString);

                    usersList();       
                }
            }); 
            
        });	

        $(".usersSearch").click(function(event) {  
            event.preventDefault();

            var options = $('#usersSearchOptions').val();
            var data = "action=books-search&search="+options;

            $.ajax({
                type: "POST",
                data: data,
                url: "api-router.php",
                dataType: 'json',
                success: function(data) {	
                    var dataString = JSON.stringify(data);
                    $('#usersSearchContent').val(dataString);

                    usersList();       
                }
            }); 
            
        });	

    });
  
  </script>

</head>

<body>

    <h1><strong>V&P API test</strong></h1>

    <p>
        API przyjmuje dane wysłane tylko jako $_POST<br>
        Podstawą jest plik "api-router.php", który kieruje ruch poprzez zmienną "action".<br><br>

        Dane połączeniowe MySQL znajdują się w pliku "api-connect.php".<br><br>
        
        <strong>'users-list': $Api->usersList();</strong><br>
        Nie wymaga argumentów. Wyświetla listę użytkowników.<br><br>
        
        <strong>'users-add': $Api->usersAdd();</strong><br>
        Wymaga "firstname", "lastname", "email". Opcjonalnie "slug". Dodaje użytkownika do bazy lub wyświetla błąd.<br><br>

        <strong>'users-edit': $Api->usersEdit();</strong><br>
        Wymaga "firstname", "lastname", "email", "id". Opcjonalnie "slug". Zmienia dane użytkownika "firstname", "lastname", "slug" lub wyświetla błąd.<br><br>

        <strong>'users-del': $Api->usersDel();</strong><br>
        Wymaga "id". Usuwa użytkownika i powiązania z tabeli "users_books". Po usunięciu wysyła SMS na domyślny numer telefonu z pliku "api-sms.php" o treściu "ID # usunięte!".<br><br>
        
        <strong>'books-add': $Api->booksAdd();</strong><br>
        Wymaga "title", "description", "shortDescription". Dodaje książkę do bazy danych.<br><br>

        <strong>'books-edit': $Api->booksEdit();</strong><br>
        Wymaga "id", "title", "description", "shortDescription". Edytuje dane książki w bazie danych.<br><br>

        <strong>'books-del': $Api->booksDel();</strong><br>
        Wymaga "id". Usuwa książkę i powiązania z tabeli "users_books".<br><br>

        <strong>'books-search': $Api->booksSearch();</strong><br>
        Wymaga "search". Szuka w "title" książki szukanej frazy "search".<br><br>
    
        <strong>'user-book-add': $Api->userBookAdd();</strong><br>
        Wymaga "id_user", "id_book". Sprawdza czy powiązanie istnieje. Sprawdza czy użytkownik istnieje. Sprawdza czy książka istnieje. Jeśli nie ma powiązania, 
        książka i użytkownik istnieją w bazie, wtedy dodaje powiązanie książki do użytkownika w tabeli "users_books"<br><br>

        <strong>'user-book-list': $Api->userBookList();</strong><br>
        Wymaga "id_user". Wyświetla książki przypisane do użytkownika.

    </p>

    <h1>Demo live</h1>

    <div class="usersList" style="cursor:pointer;">&raquo; User list [CLICK ME]</div>
    <textarea cols="100" rows="10" id="usersListContent" style="font-size:10px;"></textarea>
    
    <hr>
    
    <select name="usersAddOptions" id="usersAddOptions">
        <option value="userA">random user 1</option>
        <option value="userB">random user 2</option>
        <option value="userC">random user 3</option>
        <option value="userD">random user 4 - wrong email</option>
        <option value="empty">Empty user</option>
    </select><br>
    <div class="usersAdd" style="cursor:pointer;">&raquo; Add selected user [CLICK ME]</div>
    <textarea cols="100" rows="2" id="usersAddContent" style="font-size:10px;"></textarea>

    <hr>
    
    <div class="usersEdit" style="cursor:pointer;">&raquo; Edit default user [ID=1], random SLUG [CLICK ME]</div>
    <textarea cols="100" rows="2" id="usersEditContent" style="font-size:10px;"></textarea>

    <hr>
    
    <select name="usersSearchOptions" id="usersSearchOptions">
        <option value="">WSZYSTKIE</option>
        <option value="nieznalezione">BRAK WYNIKÓW</option>
        <option value="tad">tad</option>
        <option value="kaczka">kaczka</option>
        <option value="szczęściu">szczęściu</option>
    </select><br>
    <div class="usersSearch" style="cursor:pointer;">&raquo; Search [CLICK ME]</div>
    <textarea cols="100" rows="20" id="usersSearchContent" style="font-size:10px;"></textarea>

</body>

</html>
