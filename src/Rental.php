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
}
?>