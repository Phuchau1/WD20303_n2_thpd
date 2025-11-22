<?php
class Order {
    private $db;
    public function __construct($db){ $this->db = $db; }

    // Simple examples for admin
    public function all(){
        return $this->db->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    }

    public function find($id){
        $order = $this->db->queryOne("SELECT * FROM orders WHERE id = ?", [$id]);
        if ($order) {
            $order['items'] = $this->db->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?", [$id])->fetchAll();
        }
        return $order;
    }

    public function updateStatus($id, $status){
        return $this->db->execute("UPDATE orders SET status = ? WHERE id = ?", [$status, $id]);
    }

    /**
     * Create order with items (transaction)
     * $items = [ ['product_id'=>int,'quantity'=>int,'price'=>int], ... ]
     */
    public function create($customer_name, $phone, $address, $total_price, $items, $email = null){
        $pdo = $this->db->getPdo();
        try{
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, email, address, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$customer_name, $phone, $email, $address, $total_price]);
            $orderId = $pdo->lastInsertId();
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach($items as $it){
                $stmtItem->execute([$orderId, $it['product_id'], $it['quantity'], $it['price']]);
            }
            $pdo->commit();
            return $orderId;
        } catch (Exception $e){
            $pdo->rollBack();
            return false;
        }
    }
}
?>
