<?php

namespace app\controllers;

use app\core\Controller;

abstract class Product extends Controller
{
    protected string $SKU;
    protected string $name;
    protected string $price;
    protected string $type;

    /**
     * Product constructor assigns values to protected properties.
     * @param $SKU
     * @param $name
     * @param $price
     * @param $type
     */
    public function __construct($SKU, $name, $price, $type)
    {
        parent::__construct();
        $this->SKU = $SKU;
        $this->name = $name;
        $this->price = $price;
        $this->type = $type;
    }

    /**
     * This method is used to insert data into the database that is same for all subclasses
     */
    protected function insert_into_product()
    {
        $sql = $this->db->prepare("INSERT INTO product(sku, name, price, type) VALUES (?, ?, ?, ?)");
        $sql->execute([$this->SKU, $this->name, $this->price, $this->type]);
    }

    /**
     *
     * This method is used to insert product-specific data into the database
     *
     * @param $data
     * @return mixed
     */
    abstract function insert_into_db($data);
}

class Dvd extends Product
{
    function insert_into_db($data)
    {
        $data["sku_id"] = $this->SKU;
        $this->insert_into_product();

        $this->db->prepare("INSERT INTO dvd(size_mb, sku) VALUES (?, ?)")->execute(array_values($data));
    }
}

class Book extends Product
{
    function insert_into_db($data)
    {
        $data["sku_id"] = $this->SKU;
        $this->insert_into_product();

        $this->db->prepare("INSERT INTO book(weight_kg, sku) VALUES (?, ?)")->execute(array_values($data));
    }
}

class Furniture extends Product
{
    function insert_into_db($data)
    {
        $data["sku_id"] = $this->SKU;
        $this->insert_into_product();

        $sql = $this->db->prepare("INSERT INTO furniture(height_cm, width_cm, length_cm, sku) VALUES (?, ?, ?, ?)");
        $sql->execute(array_values($data));
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
            "sku" => function ($data) {if (strlen($data) == 0) return "Please, submit required data";
                                        elseif ($this->rowExists("product", "sku", $data)) return "SKU must be unique"; return true;},
            "name" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; return true;},
            "price" => function ($data) {if (strlen($data) == 0) return "Please, submit required data";
                                            elseif (!is_numeric($data)) return "Please, provide the data of indicated type"; return true;},
            "type_switcher" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; return true;},
            "size_mb" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; elseif (!is_numeric($data)) return "Please, provide the data of indicated type"; return true;},
            "weight_kg" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; elseif (!is_numeric($data)) return "Please, provide the data of indicated type"; return true;},
            "height_cm" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; elseif (!is_numeric($data)) return "Please, provide the data of indicated type"; return true;},
            "width_cm" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; elseif (!is_numeric($data)) return "Please, provide the data of indicated type"; return true;},
            "length_cm" => function ($data) {if (strlen($data) == 0) return "Please, submit required data"; elseif (!is_numeric($data)) return "Please, provide the data of indicated type"; return true;},
        ];

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
                $product_type = ucfirst($post_data["type_switcher"]);
                $product_specific_data = array_slice($post_data, 4);

                // Create the instance of the class according to the type
                $class = "\app\controllers\\$product_type";
                $object = new $class($post_data["sku"], $post_data["name"], $post_data["price"], $post_data["type_switcher"]);
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
            foreach ($_POST as $value)
            {
                foreach ($value as $sku)
                {
                    // determine what type is the product which is same as the table name
                    $product_type = $this->db->prepare("SELECT type FROM product WHERE sku = ?");
                    $product_type->execute([$sku]);
                    $table_name = $product_type->fetch($this->db::FETCH_ASSOC)["type"];

                    $this->db->prepare("DELETE FROM $table_name WHERE sku = ?")->execute([$sku]);
                    $this->db->prepare("DELETE FROM product WHERE sku = ?")->execute([$sku]);
                }
            }
        }
    }
}