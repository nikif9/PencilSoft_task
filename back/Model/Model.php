<?php
    // Интерфейс для класса Record
    interface RecordInterface
    {
        public function getAll();
        public function getOne($id);
        public function create($date, $sum, $comment);
        public function update($id, $date, $sum, $comment);
        public function delete($id);
    }

    class Record implements RecordInterface
    {
        // Свойства класса для хранения информации о базе данных
        private $db;

        /**
         * Record constructor.
         *
         * @param PDO $db
         */
        public function __construct(PDO $db)
        {
            $this->db = $db;
        }

        /**
         * Получение всех записей из таблицы records
         *
         * @return array
         */
        public function getAll()
        {
            $stmt = $this->db->prepare("SELECT * FROM records");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Получение одной записи из таблицы records по id
         *
         * @param int $id
         * @return array
         */
        public function getOne($id)
        {
            $stmt = $this->db->prepare("SELECT * FROM records WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        /**
         * Создание новой записи в таблице records
         *
         * @param string $date
         * @param float $sum
         * @param string $comment
         * @return array
         */
        public function create($date, $sum, $comment)
        {
            $stmt = $this->db->prepare("INSERT INTO records (date, sum, comment) VALUES (:date, :sum, :comment)");
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':sum', $sum);
            $stmt->bindParam(':comment', $comment);
            $stmt->execute();

            // Возвращаем только что созданную запись
            return $this->getOne($this->db->lastInsertId());
        }

        /**
         * Обновление существующей записи в таблице records
         *
         * @param int $id
         * @param string $date
         * @param float $sum
         * @param string $comment
         * @return array
         */
        public function update($id, $date, $sum, $comment)
        {
            $stmt = $this->db->prepare("UPDATE records SET date = :date, sum = :sum, comment = :comment WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':sum', $sum);
            $stmt->bindParam(':comment', $comment);
            $stmt->execute();

            // Возвращаем обновленную запись
            return $this->getOne($id);
        }

        /**
         * Удаление записи из таблицы records
         *
         * @param int $id
         * @return bool
         * @throws Exception
         */
        public function delete($id)
        {
            $stmt = $this->db->prepare("DELETE FROM records WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("Record deletion failed");
            }
        }
    }
