<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Массив для временного хранения сообщений пользователю.
    $messages = array();
    // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '');
        $messages['success'] = 'Спасибо, результаты сохранены.';
    }

    // Складываем признак ошибок в массив.
    $errors = array();
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);

    if ($errors['name']) {
        $messages['name'] = 'Заполните имя латиницей<br>';
        setcookie('name_error', '');
    }
    
    if ($errors['email']) {
        $messages['email'] = 'Заполните почту правильно<br>';
        setcookie('email_error', '');
    }

    if ($errors['bio']) {
        $messages['bio'] = 'Заполните биографию латиницей<br>';
        setcookie('bio_error', '');
    }


    // Складываем предыдущие значения полей в массив, если есть.
    $values = array();
    $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['birthday'] = empty($_COOKIE['birthday_value']) ? '' : $_COOKIE['birthday_value'];
    $values['sex'] = empty($_COOKIE['sex_value']) ? '' : $_COOKIE['sex_value'];
    $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : $_COOKIE['limbs_value'];
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    $values['contract'] = empty($_COOKIE['contract_value']) ? '' : $_COOKIE['contract_value'];

    $values['powers'] = [];
    $powersCookie = array();
    if (!empty($_COOKIE['powers_value'])) {
        $powersCookie = (array)json_decode($_COOKIE['powers_value']);
        foreach ($powersCookie as $power) {
            $values['powers'][$power] = $power;
        }
    }
    // Включаем содержимое файла form.php.
    // В нем будут доступны переменные $messages, $errors и $values для вывода
    // сообщений, полей с ранее заполненными данными и признаками ошибок.
    include('form.php');
    exit();
} else {
    $name = $_POST['field-name'];
    $email = $_POST['field-email'];
    $birthday = $_POST['field-date'];
    $sex = $_POST['radio-group-1'];
    $limbs = $_POST['radio-group-2'];
    $powers = $_POST['field-power'];
    $bio = $_POST['field-biography'];
    $contract = $_POST['cntrt'];

    $errors = FALSE;


    if (!preg_match("/^[A-z]*$/", $name)) {
        setcookie('name_error', '1');
        $errors = TRUE;
    } else {
        setcookie('name_value', $name, time() + 12 * 30 * 24 * 60 * 60);
    }

    if (!preg_match("/^[A-z]*$/", $email)) {
        setcookie('email_error', '1');
        $errors = TRUE;
    } else {
        setcookie('email_value', $email, time() + 12 * 30 * 24 * 60 * 60);
    }
    
    if (!preg_match("/^[A-z]*$/", $bio)) {
        setcookie('bio_error', '1');
        $errors = TRUE;
    } else {
        setcookie('bio_value', $bio, time() + 12 * 30 * 24 * 60 * 60);
    }

    setcookie('powers_value', json_encode($powers), time() + 12 * 30 * 24 * 60 * 60);

    setcookie('birthday_value', $birthday, time() + 12 * 30 * 24 * 60 * 60);

    setcookie('sex_value', $sex, time() + 12 * 30 * 24 * 60 * 60);

    setcookie('limbs_value', $limbs, time() + 12 * 30 * 24 * 60 * 60);

    setcookie('contract_value', $contract, time() + 12 * 30 * 24 * 60 * 60);


    if ($errors) {
        header('Location: index.php');
        exit();

    } else {
        setcookie('name_error', '');
        setcookie('email_error', '');
        setcookie('bio_error', '');
    }

    setcookie('save', '1', time() + 12 * 30 * 24 * 60 * 60);
    try {

        $user = 'u53890';
        $password = '8091112';

        $connection = new PDO("mysql:host=localhost;dbname=u53890", $user, $password, array(PDO::ATTR_PERSISTENT => true));


        $query1 = "INSERT INTO form (name, email, birthday, sex, limbs, bio, contract) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $ex1 = $connection->prepare($query1);

        $ex1->execute(array($name, $email, $birthday, $sex, $limbs, $bio, $contract));
        $id_user = $connection->lastInsertId();

        $query2 = "INSERT INTO form_power (form_id, power_id) VALUES (:form_id, (SELECT id FROM powers WHERE power=:power))";

        $ex2 = $connection->prepare($query2);

        foreach ($powers as $power) {
            $ex2->bindParam(':form_id', $id_user);
            $ex2->bindParam(':power', $power);
            $ex2->execute();
        }

    } catch (PDOException $e) {
        print('Database error : ' . $e->getMessage());
        exit();
    }
}
header('Location: ?save=1');
