<?php
class Rental
{
    private $conn;
    private $table_name = "rentals";

    public $id;
    public $car_id;
    public $customer_name;
    public $rental_date;
    public $return_date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (car_id, customer_name, rental_date) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->car_id = htmlspecialchars(strip_tags($this->car_id));
        $this->customer_name = htmlspecialchars(strip_tags($this->customer_name));
        $this->rental_date = htmlspecialchars(strip_tags($this->rental_date));

        $stmt->bind_param("iss", $this->car_id, $this->customer_name, $this->rental_date);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function readAll()
    {
        $query = "SELECT r.id, r.car_id, r.customer_name, r.rental_date, r.return_date, c.name as car_name, c.price as car_price
                  FROM " . $this->table_name . " r
                  LEFT JOIN cars c ON r.car_id = c.id
                  ORDER BY r.rental_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function update()
    {
        $query = "UPDATE " . $this->table_name . " SET return_date = ? WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->return_date = htmlspecialchars(strip_tags($this->return_date));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("si", $this->return_date, $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
