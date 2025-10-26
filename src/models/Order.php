<?php
class Order {
    private $pdo;
    private $userId;
    private $items = [];

    public function __construct($pdo, $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;
    }

    public function addItem($productId, $quantity) {
        $this->items[$productId] = $quantity;
    }

    public function finalize() {
        try {
            $this->pdo->beginTransaction();
            foreach($this->items as $productId => $quantity){
                $stmt = $this->pdo->prepare("INSERT INTO pedidos (user_id, product_id, quantidade) VALUES (?, ?, ?)");
                $stmt->execute([$this->userId, $productId, $quantity]);
            }
            $this->pdo->commit();
            return true;
        } catch(PDOException $e){
            $this->pdo->rollBack();
            error_log("Erro finalizar pedido: " . $e->getMessage());
            return false;
        }
    }
}
