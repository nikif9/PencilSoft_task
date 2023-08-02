<?php
// Подключение класса контроллера
require_once dirname(__FILE__) . '/Controler/Controler.php';

// Заголовки, разрешающие кросс-доменные запросы
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
// Получаем URI
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Определяем возможные маршруты
$routes = [
    'hello' => function () {
        return "Hello, world!";
    },
    // Обрабатывает запросы к ресурсу "expense"
    'expense' => function ($id) {
        return handleExpenseRequest($id);
    },
];

// Разбиваем URI на части
$uri_parts = explode('/', $uri);

// Проверяем, существует ли такой маршрут
if (array_key_exists($uri_parts[0], $routes)) {
    // Если существует, то вызываем соответствующую функцию и передаем ей параметры
    echo $routes[$uri_parts[0]]($uri_parts[1] ?? '');
} else {
    // Если маршрута не существует, то возвращаем ошибку 404
    http_response_code(404);
    echo "404 Not Found";
}

// Функция для обработки запросов к ресурсу "expense"
function handleExpenseRequest($id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Если метод запроса - POST, то создаем новую запись
        $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $result = create($postData);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Если метод запроса - GET, то возвращаем одну или все записи
        if ($id === '') {
            $result = getAll();
        } else {
            $result = getOne($id);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
        // Если метод запроса - PATCH, то обновляем запись
        $postData = getDecodedInputData();
        $result = update($id, $postData);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Если метод запроса - DELETE, то удаляем запись
        $result = delete($id);
    }

     // Возвращаем результат в формате JSON
    return json_encode($result);
}

// Функция для получения и декодирования данных из тела запроса
function getDecodedInputData() {
    $rawData = file_get_contents("php://input");
    $postData = json_decode($rawData, true);
    if ($postData === null && json_last_error() !== JSON_ERROR_NONE) {
        die('Ошибка декодирования JSON!');  // or handle the error in another way
    }
    
    return $postData;
}
