<?php
class Car
{
    private $conn;
    private $table_name = "cars";

    public $id;
    public $name;
    public $image;
    public $price;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function readAll()
    {
        $query = "SELECT id, name, image, price, status FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (name, image, price) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->price = htmlspecialchars(strip_tags($this->price));

        $stmt->bind_param("ssd", $this->name, $this->image, $this->price);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function updateStatus()
    {
        $query = "UPDATE " . $this->table_name . " SET status = ? WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("si", $this->status, $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function readOne()
    {
        $query = "SELECT name, image, price, status FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        $this->name = $result['name'];
        $this->image = $result['image'];
        $this->price = $result['price'];
        $this->status = $result['status'];
    }

    function update()
    {
        $query = "UPDATE " . $this->table_name . " SET name = ?, price = ?, status = ? WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("sdsi", $this->name, $this->price, $this->status, $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("i", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function getStatusCounts()
    {
        $query = "SELECT status, COUNT(*) as count FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>