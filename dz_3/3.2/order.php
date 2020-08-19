<?php
$databaseName = 'demo_evd';
$databaseUser = 'root';
$databasePassword = '';

// Создаем базу данных ечли ее нет
$link = mysqli_connect('localhost', $databaseUser, $databasePassword);
if (!$link) {
    die('Could not connect: ' . mysqli_error($link));
}

// Make my_db the current database
$db_selected = mysqli_select_db($link, $databaseName);

if (!$db_selected) {
    // If we couldn't, then it either doesn't exist, or we can't see it.
    $sql = "CREATE DATABASE $databaseName";

    if (mysqli_query($link, $sql)) {
        echo "Database my_db created successfully\n";
    } else {
        echo 'Error creating database: ' . mysqli_error($link) . "\n";
    }
}

mysqli_close($link);


// SQL заготовки
$usersTable = 'users';
$ordersTable = 'orders';

$sqlCreateUsers = "CREATE TABLE `$usersTable` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
                     `name` VARCHAR(255) NOT NULL , 
                     `phone` VARCHAR(30) NOT NULL , 
                     `email` VARCHAR(255) NOT NULL ,
                      PRIMARY KEY (`id`)
                  )";

$sqlCreateOrders = "CREATE TABLE `$ordersTable` ( 
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `user_id` INT UNSIGNED NOT NULL ,
                    `address` VARCHAR(255) NOT NULL ,
                    `comment` TEXT NOT NULL ,
                    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`), 
                    INDEX (`user_id`),
                    FOREIGN KEY (user_id)
                    REFERENCES $usersTable(id) ON UPDATE CASCADE
                    ON DELETE CASCADE
                   )";

$sqlFindUser = "SELECT * from `$usersTable` WHERE email = :email LIMIT 1";
$sqlCreateUser = "INSERT INTO `$usersTable` 
                    (`name`, `phone`, `email`) 
                    VALUES
                    (:name, :phone, :email)";
$sqlUpdateUser = "UPDATE `$usersTable` SET
                  `name` = :name,
                  `phone` = :phone";

$sqlCreateOrder = "INSERT INTO `$ordersTable` 
                    (`user_id`, `address`, `comment`) 
                    VALUES
                    (:user_id, :address, :comment)";

$sqlUserOrdersCount = "SELECT COUNT(*) FROM $ordersTable WHERE user_id = :user_id";

// Подключаемся по человечески
try {
    $pdo = new PDO("mysql:host=localhost;dbname=$databaseName", $databaseUser, $databasePassword);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage();
    die();
}

// создаем таблицы если нету
$pdo->exec($sqlCreateUsers);
$pdo->exec($sqlCreateOrders);

//Получаем данные пользователя из ГЕТ запроса (так как в форме не указан метод - будет гет запрос по дефолту)
$userData = [];
$userData['name'] = $_GET['name'] ?? die('не все поля заполнены');
$userData['phone'] = $_GET['phone'] ?? die('не все поля заполнены');
$userData['email'] = $_GET['email'] ?? die('не все поля заполнены');
$userData['street'] = $_GET['street'] ?? die('не все поля заполнены');
$userData['home'] = $_GET['home'] ?? die('не все поля заполнены');
$userData['part'] = $_GET['part'] ?? die('не все поля заполнены');
$userData['appt'] = $_GET['appt'] ?? die('не все поля заполнены');
$userData['floor'] = $_GET['floor'] ?? die('не все поля заполнены');
$userData['comment'] = $_GET['comment'] ?? '';
$userData['payment'] = $_GET['payment'] ?? die('не все поля заполнены');
$userData['callback'] = $_GET['callback'] ?? false;

//если не все поля заполнены - падаем

// ищем пользователя
$stmt = $pdo->prepare($sqlFindUser);
$stmt->execute(['email' => $userData['email']]);


$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    //Создаем пользователя
    $stmt2 = $pdo->prepare($sqlCreateUser);
    $stmt2->execute([
      'name' => $userData['name'],
      'phone' => $userData['phone'],
      'email' => $userData['email'],
    ]);
    $user_id = $pdo->lastInsertId();
} else {
    //Обновляем пользовательские данные
    $stmt2 = $pdo->prepare($sqlUpdateUser);
    $stmt2->execute([
      'name' => $userData['name'],
      'phone' => $userData['phone'],
    ]);

    $user_id = $user['id'];
}


$address = "ул {$userData['street']}, 
            дом {$userData['home']},
            корпус {$userData['part']},
            квартира {$userData['appt']}, 
            этаж: {$userData['floor']}";
$comment = "Оплата: {$userData['payment']}<br>Нужно перезвонить: {$userData['callback']}<br><br>{$userData['comment']}";

$stmt3 = $pdo->prepare($sqlCreateOrder);
$stmt3->execute([
  'user_id' => $user_id,
  'address' => $address,
  'comment' => $comment,
]);
$order_id = $pdo->lastInsertId();

$stmt4 = $pdo->prepare($sqlUserOrdersCount);
$stmt4->execute([
  'user_id' => $user_id,
]);
$number_of_rows = $stmt4->fetchColumn();



echo "Спасибо, ваш заказ будет доставлен по адресу: $address";
echo '<br>';
echo "Номер вашего заказа: #$order_id";
echo '<br>';
echo "Это ваш $number_of_rows-й заказ!";
