<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Соединение не удалось: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fio = htmlspecialchars($_POST['fio']);
    $age = htmlspecialchars($_POST['age']);
    $phone = htmlspecialchars($_POST['phone']);

    if (substr_count($fio, ' ') != 2) {
        $errData = 'ФИО указано неверно!';
    }

    if (strlen($phone) != 17) {
        $errData .= ' Номер телефона указан неверно!';
    }

    if (!isset($errData)) {
        $stmt = $conn->prepare("INSERT INTO users (fio, age, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fio, $age, $phone);

        if ($stmt->execute()) {

            $webhookUrl = "https://b24-as0ccq.bitrix24.by/rest/1/y1a6rlab919fywm1/crm.lead.add.json";

            $userData = array(
                "fields" => array(
                    "TITLE" => "Новый пользователь",
                    "NAME" => $fio,
                    "AGE" => $age,
                    "PHONE" => array(
                        array("VALUE" => $phone, "VALUE_TYPE" => "WORK")
                    )
                ),
                "params" => array("REGISTER_SONET_EVENT" => "Y")
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $webhookUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($userData),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "Ошибка #:" . $err;
            } else {
                echo "Форма успешно отправлена!";
            }
        } else {
            echo "Ошибка: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo $errData;
    }
} else {
    echo "Неверный запрос";
}

$conn->close();
