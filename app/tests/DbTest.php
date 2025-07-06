<?php

use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
    private \PDO $pdo;

    public function testConnection(): void
    {
        $version = $this->pdo->query('SELECT VERSION()')->fetchColumn();
        $this->assertIsString($version, 'Версия MySQL должна быть строкой');
    }

    public function testTableCanBeCreated(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS temp_test');

        $this->pdo->exec(<<<'SQL'
CREATE TABLE temp_test (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL
        );

        $stmt = $this->pdo->query("SHOW TABLES LIKE 'temp_test'");
        $this->assertNotFalse($stmt->fetchColumn(), 'Таблица temp_test не создана');
    }

    public function testInsertAndSelect(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS temp_test');
        $this->pdo->exec(<<<'SQL'
CREATE TABLE temp_test (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL
        );

        $stmt = $this->pdo->prepare('INSERT INTO temp_test (name) VALUES (:name)');
        $stmt->execute([':name' => 'Alice']);
        $lastId = $this->pdo->lastInsertId();
        $this->assertGreaterThan(0, $lastId, 'lastInsertId должен быть > 0');

        $stmt = $this->pdo->prepare('SELECT name FROM temp_test WHERE id = ?');
        $stmt->execute([$lastId]);
        $this->assertEquals('Alice', $stmt->fetchColumn(), 'Имя не совпало после вставки');
    }

    public function testUpdateAndDelete(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS temp_test');
        $this->pdo->exec(<<<'SQL'
CREATE TABLE temp_test (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL
        );
        $this->pdo->exec("INSERT INTO temp_test (value) VALUES (10), (20)");

        $rows = $this->pdo->exec('UPDATE temp_test SET value = value + 5 WHERE value = 10');
        $this->assertEquals(1, $rows, 'Должна быть обновлена ровно одна строка');

        $count = (int)$this->pdo->query('SELECT COUNT(*) FROM temp_test WHERE value = 15')->fetchColumn();
        $this->assertEquals(1, $count, 'Обновлённое значение не найдено');

        $rows = $this->pdo->exec('DELETE FROM temp_test WHERE value = 20');
        $this->assertEquals(1, $rows, 'Должна быть удалена ровно одна строка');

        $countAll = (int)$this->pdo->query('SELECT COUNT(*) FROM temp_test')->fetchColumn();
        $this->assertEquals(1, $countAll, 'Ожидается одна оставшаяся строка');
    }

    public function testTransactionRollback(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS temp_test');
        $this->pdo->exec(<<<'SQL'
CREATE TABLE temp_test (
    id INT AUTO_INCREMENT PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL
        );

        $this->pdo->beginTransaction();
        $this->pdo->exec('INSERT INTO temp_test VALUES ()');
        $countInTx = (int)$this->pdo->query('SELECT COUNT(*) FROM temp_test')->fetchColumn();
        $this->assertEquals(1, $countInTx, 'В транзакции должна быть одна запись');

        $this->pdo->rollBack();
        $countAfter = (int)$this->pdo->query('SELECT COUNT(*) FROM temp_test')->fetchColumn();
        $this->assertEquals(0, $countAfter, 'После rollback таблица должна быть пуста');
    }

    protected function setUp(): void
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');

        $this->pdo = new \PDO(
            "mysql:host={$host};port={$port};dbname={$db}",
            $user,
            $pass,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }
}
