<?php

namespace App\Database;

use PDO;

class PdoVendingRepository
{
    public function __construct(private readonly PdoConnection $connection)
    {
    }

    public function createProduct(array $data): int
    {
        $pdo = $this->connection->connection();
        $statement = $pdo->prepare('INSERT INTO products (name, price, quantity_available, created_at, updated_at) VALUES (:name, :price, :quantity_available, NOW(), NOW())');
        $statement->execute($data);

        return (int) $pdo->lastInsertId();
    }

    public function updateProduct(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = $this->connection->connection()->prepare('UPDATE products SET name = :name, price = :price, quantity_available = :quantity_available, updated_at = NOW() WHERE id = :id');
        $statement->execute($data);
    }

    public function deleteProduct(int $id): void
    {
        $statement = $this->connection->connection()->prepare('DELETE FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function findProduct(int $id): ?array
    {
        $statement = $this->connection->connection()->prepare('SELECT * FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);

        $product = $statement->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }
}
