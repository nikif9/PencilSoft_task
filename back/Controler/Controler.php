<?php
    // Подключаем модель
    require_once dirname(__FILE__).'/../Model/Model.php';
    // Подключаем конфигурационный файл
    require_once dirname(__FILE__).'/../config.php';

    try {
        // Создание нового подключения к базе данных
        $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Создание нового объекта Record с передачей в него подключения к базе данных
        $record = new Record($db);

    
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    /**
     * Получить все записи
     *
     * @return array
     */
    function getAll() {
        global $record;
        $result = $record->getAll();
        return $result;
    }

     /**
     * Получить одну запись по идентификатору
     *
     * @param int $id
     * @return array
     */
    function getOne($id) {
        global $record;
        $result = $record->getOne($id);
        return $result;
    }

    /**
     * Обработка входящих данных и выполнение операции (создание или обновление записи)
     *
     * @param object $record
     * @param array $postData
     * @param string $operation
     * @param int|null $id
     * @return array
     */
    function processData($record, $postData, $operation, $id = null) {
        $comment = ''; 
        $sum = '';
        $date = '';
        $validationResult = validatePostData($postData);

        if ($validationResult['success']) {
            $date = $validationResult['data']['date'];
            $sum = $validationResult['data']['sum'];
            $comment = $validationResult['data']['comment'];

            try {
                $resultDB = ($operation === 'create') ? $record->create($date, $sum, $comment) : $record->update($id, $date, $sum, $comment);
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'notification' => [
                        'title' => 'Ошибка базы данных',
                        'type' => 'error'
                    ]
                ];
            }

            if (count($resultDB) > 0) {
                $result = [
                    'success' => true,
                    'notification' => [
                        'title' => ($operation === 'create') ? 'Позиция добавлена' : 'Изменения сохранены',
                        'type' => 'success'
                    ]
                ];
            } else {
                $result = [
                    'success' => false,
                    'notification' => [
                        'title' => 'Ошибка базы данных',
                        'type' => 'error'
                    ]
                ];
            }
        } else {
            $result = $validationResult;
        }

        return $result;
    }
    
     /**
     * Создание новой записи
     *
     * @param array $postData
     * @return array
     */
    function create($postData) {
        global $record;
        return processData($record, $postData, 'create');
    }

    /**
     * Обновление существующей записи
     *
     * @param int $id
     * @param array $postData
     * @return array
     */
    function update($id, $postData) {
        global $record;
        return processData($record, $postData, 'update', $id);
    }

     /**
     * Удаление записи
     *
     * @param int $id
     * @return array
     */
    function delete($id) {
        global $record;

        if ($id !== '') {
            try {
                $resultDB = $record->delete($id);
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'notification' => [
                        'title' => 'Ошибка базы данных',
                        'type' => 'error'
                    ]
                ];
            }
            if ($resultDB) {
                $result = [
                    'success' => true,
                    'notification' => [
                        'title' => 'Позиция Удалена',
                        'type' => 'success'
                    ]
                ];
            } else {
                $result = [
                    'success' => false,
                    'notification' => [
                        'title' => 'ошибка базы данных',
                        'type' => 'error'
                    ]
                ];
            }
        } else {
            $result = [
                'success' => false,
                'notification' => [
                    'title' => 'не Выбрано что удалять',
                    'type' => 'error'
                ]
            ];
        }

        return $result;
    }

    /**
     * Валидация даты
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    function validateDate($date, $format = 'Y-m-d H:i') {
        
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Валидация суммы
     *
     * @param string $sum
     * @return bool
     */
    function validateSum($sum) {
        return is_numeric($sum);
    }

    /**
     * Валидация входящих данных
     *
     * @param array $postData
     * @return array
     */
    function validatePostData($postData) {
        $comment = '';
        $sum = '';
        $date = '';
        
        try {
            if (isset($postData['comment'])) {
                $comment = trim($postData['comment']);
            }
            if (isset($postData['sum'])) {
                $sum = trim($postData['sum']);
            }
            if (isset($postData['date'])) {
                $date = trim($postData['date']);
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'notification' => [
                    'title' => 'Ошибка обработки ввода',
                    'type' => 'error'
                ]
            ];
        }

        if ($comment === '') {
            return [
                'success' => false,
                'notification' => [
                    'title' => 'пустой текст',
                    'type' => 'error'
                ]
            ];
        }
        
        if (!validateDate($date)) {
            return [
                'success' => false,
                'notification' => [
                    'title' => 'Неверный формат даты',
                    'type' => 'error'
                ]
            ];
        }
        

        if (!validateSum($sum)) {
            return [
                'success' => false,
                'notification' => [
                    'title' => 'Сумма должна быть числом',
                    'type' => 'error'
                ]
            ];
        }

        return [
            'success' => true,
            'data' => [
                'date' => $date,
                'sum' => $sum,
                'comment' => $comment
            ]
        ];
    }
