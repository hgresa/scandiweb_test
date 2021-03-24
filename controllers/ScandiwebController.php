<?php

namespace app\controllers;

use app\core\Controller;

abstract class Product extends Controller
{
    protected string $sku;
    protected string $name;
    protected string $price;
    protected string $type;

    public function __construct($fields)
    {
        parent::__construct();
        foreach ($fields as $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * This method is used to insert data into the database that is same for all subclasses
     */
    protected function insert_into_product()
    {
        $sql = $this->db->prepare("INSERT INTO product(sku, name, price, type) VALUES (?, ?, ?, ?)");
        $sql->execute([$this->sku, $this->name, $this->price, $this->type]);
    }

    /**
     *
     * This method is used to insert product-specific data into the database
     *
     * @return mixed
     */
    abstract function insert_into_db();
}

class Dvd extends Product
{
    protected int $size_mb;

    function insert_into_db()
    {
        $this->insert_into_product();

        $sql = $this->db->prepare("INSERT INTO dvd(size_mb, sku) VALUES (?, ?)");
        $sql->execute([$this->size_mb, $this->sku]);
    }
}

class Book extends Product
{
    protected int $weight_kg;

    function insert_into_db()
    {
        $this->insert_into_product();

        $sql = $this->db->prepare("INSERT INTO book(weight_kg, sku) VALUES (?, ?)");
        $sql->execute([$this->weight_kg, $this->sku]);
    }
}

class Furniture extends Product
{
    protected int $height_cm;
    protected int $width_cm;
    protected int $length_cm;

    function insert_into_db()
    {
        $this->insert_into_product();

        $sql = $this->db->prepare("INSERT INTO furniture(height_cm, width_cm, length_cm, sku) VALUES (?, ?, ?, ?)");
        $sql->execute([$this->height_cm, $this->width_cm, $this->length_cm, $this->sku]);
    }
}

/**
 *
 * CRUD functionality is implemented in this controller
 *
 * Class ScandiwebController
 * @package app\controllers
 */
class ScandiwebController extends Controller
{
    /**
     *
     * This method is used to check if row of any table exists in the database
     *
     * @param $table
     * @param $where_column
     * @param $data
     * @return bool
     */
    private function rowExists($table, $where_column, $data)
    {
        $sql = $this->db->prepare("SELECT * FROM $table WHERE $where_column = ?");
        $sql->execute([$data]);
        if ($sql->fetch())
        {
            return true;
        }
        return false;
    }

    /**
     *
     * This method is used to validate the submitted data
     *
     * @param $data
     * @return bool|array
     */
    private function validate_form($data): bool|array
    {
        $error_list = [];
        $validated_data = [];

        $rules =
        [
            "sku" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; elseif ($this->rowExists("product", "sku", $data)) return "SKU must be unique"; return true;},
        ];

        foreach (["price", "size_mb", "weight_kg", "height_cm", "width_cm", "length_cm"] as $field_name)
        {
            $rules[$field_name] = function ($data) {if (strlen($data) == 0) return "Please, submit required data"; elseif (!is_numeric($data)) return "Please, provide the data of indicated type"; return true;};
        }
        foreach (["name", "type"] as $field_name)
        {
            $rules[$field_name] = function ($data) {if (strlen($data) == 0) return "Please, submit required data"; return true;};
        }

        foreach($data as $key => $value)
        {
            $validated_field = $rules[$key]($value);
            if ($validated_field == 1)
            {
                $validated_data[$key] = 1;
            }
            else
            {
                $error_list[$key] = $validated_field;
            }
        }

        if (count($validated_data) == count(array_keys($data)))
        {
            return 1;
        }
        return $error_list;
    }

    /**
     *
     * This method returns the product list view file and populates it with data from the database
     *
     * @return string
     */
    public function list_product(): string
    {
        $product_specific_units = ["dvd" => ["Size" => "MB"], "book" => ["Weight" => "KG"], "furniture" => ["Dimensions" => NULL]];

        $sql = $this->db->query('SELECT p.sku, p.name, p.price, p.type, CONCAT(f.height_cm, "x", f.width_cm, "x", f.length_cm) AS measure_value 
                                            FROM product p JOIN furniture f ON (p.sku = f.sku) 
                                            UNION 
                                            SELECT p.sku, p.name, p.price, p.type, d.size_mb AS measure_value 
                                            FROM product p JOIN dvd d ON (p.sku = d.sku) 
                                            UNION 
                                            SELECT p.sku, p.name, p.price, p.type, b.weight_kg AS measure_value 
                                            FROM product p JOIN book b ON (p.sku = b.sku)');

        $params = ["sql" => $sql->fetchAll($this->db::FETCH_ASSOC), "units" => $product_specific_units];
        return $this->render("product_page", $params);
    }

    public function add_product(): string
    {
        if ($this->requestMethod() == "post")
        {
            $post_data = $this->getPostData();
            $check_errors = $this->validate_form($post_data);

            if ($check_errors != 1)
            {
                $params = ["errors" => $check_errors];
                return $this->render("add_product", $params);
            }
            else
            {
                $product_type = ucfirst($post_data["type"]);
                $product_specific_data = array_slice($post_data, 4);

                // Create the instance of the class according to the type
                $class = "\app\controllers\\$product_type";
                $object = new $class($post_data);
                $object->insert_into_db($product_specific_data);
                unset($object);

                header("Location: /product/list");
                exit;
            }
        }
        return $this->render("add_product");
    }

    public function delete_product()
    {
        if ($this->requestMethod() == "post")
        {
            $placeholders = substr(str_repeat("?, ", count($_POST["products_to_delete"])), 0, -2);

            $sql = $this->db->prepare("DELETE FROM product WHERE sku IN ($placeholders)");
            $sql->execute($_POST["products_to_delete"]);
        }
    }
}