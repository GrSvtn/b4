<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.php');
    exit();
}


try {

    $name = $_POST['field-name'];
    $email = $_POST['field-email'];
    $birthday = $_POST['field-date'];
    $sex = $_POST['radio-group-1'];
    $limbs = $_POST['radio-group-2'];
    $powers = $_POST['field-power'];
    $bio = $_POST['field-biography'];
    $contract = $_POST['cntrt'];


    $errors = FALSE;
    if (empty($name) || (!preg_match("/^[a-zA-z]*$/", $name))) {
        print('Заполните имя.<br/>');
        $errors = TRUE;
    }

    if (empty($email)) {
        print('Заполните почту.<br/>');
        $errors = TRUE;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors = TRUE;
        print('Почта заполнена некорректно.<br/>');
    }
    if (empty($bio)) {
        print('Заполните биографию.<br/>');
        $errors = TRUE;
    }
    if (empty($contract)) {
        print('Согласитесь с условиями.<br/>');
        $errors = TRUE;
    }


    if ($errors) {
        exit();
    }




    $user = 'u53890';
    $password = '8091112';

    $connection = new PDO("mysql:host=localhost;dbname=u53890", $user, $password, array(PDO::ATTR_PERSISTENT => true));


    $query1 = "INSERT INTO form (name, email, birthday, sex, limbs, bio, contract) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $ex1 = $connection->prepare($query1);

    $ex1->execute(array($name, $email, $birthday, $sex, $limbs, $bio, $contract));
    $id_user = $connection->lastInsertId();

    $query2 = "INSERT INTO form_power (form_id, power_id) VALUES (?, ?)";

    $ex2 = $connection->prepare($query2);

    foreach ($powers as $power){
        switch ($power) {
            case "Immortality":
                $ex2->execute(array($id_user, 1));
                break;
            case "Levitation":
                $ex2->execute(array($id_user, 2));
                break;
            case "Telepathy":
                $ex2->execute(array($id_user, 3));
                break;
            case "Telekinesis":
                $ex2->execute(array($id_user, 4));
                break;
        }
    }

    echo "Данные успешно сохранены";

} catch (PDOException $e) {
    print('Database error : ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');
?>
