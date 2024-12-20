<?php 
include_once __DIR__. "\..\database\config.php";
include_once __DIR__. "\..\database\operations.php";

class Product extends config implements operations {

    private $id;
    private $name_en;
    private $name_ar;
    private $code;
    private $price;
    private $image;
    private $status;
    private $quantity;
    private $desc_en;
    private $desc_ar;
    private $subcategory_id;
    private $brand_id;
    private $created_at;
    private $updated_at;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name_en
     */ 
    public function getName_en()
    {
        return $this->name_en;
    }

    /**
     * Set the value of name_en
     *
     * @return  self
     */ 
    public function setName_en($name_en)
    {
        $this->name_en = $name_en;

        return $this;
    }

    /**
     * Get the value of name_ar
     */ 
    public function getName_ar()
    {
        return $this->name_ar;
    }

    /**
     * Set the value of name_ar
     *
     * @return  self
     */ 
    public function setName_ar($name_ar)
    {
        $this->name_ar = $name_ar;

        return $this;
    }

    /**
     * Get the value of code
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @return  self
     */ 
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the value of price
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */ 
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of image
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */ 
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of quantity
     */ 
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */ 
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of desc_en
     */ 
    public function getDesc_en()
    {
        return $this->desc_en;
    }

    /**
     * Set the value of desc_en
     *
     * @return  self
     */ 
    public function setDesc_en($desc_en)
    {
        $this->desc_en = $desc_en;

        return $this;
    }

    /**
     * Get the value of desc_ar
     */ 
    public function getDesc_ar()
    {
        return $this->desc_ar;
    }

    /**
     * Set the value of desc_ar
     *
     * @return  self
     */ 
    public function setDesc_ar($desc_ar)
    {
        $this->desc_ar = $desc_ar;

        return $this;
    }

    /**
     * Get the value of subcategory_id
     */ 
    public function getSubcategory_id()
    {
        return $this->subcategory_id;
    }

    /**
     * Set the value of subcategory_id
     *
     * @return  self
     */ 
    public function setSubcategory_id($subcategory_id)
    {
        $this->subcategory_id = $subcategory_id;

        return $this;
    }

    /**
     * Get the value of brand_id
     */ 
    public function getBrand_id()
    {
        return $this->brand_id;
    }

    /**
     * Set the value of brand_id
     *
     * @return  self
     */ 
    public function setBrand_id($brand_id)
    {
        $this->brand_id = $brand_id;

        return $this;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at
     */ 
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */ 
    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function create() {
        
        
    }

    public function read() {

        $query = "SELECT id,name_en,price,desc_en,image FROM `products` WHERE `status` = $this->status ORDER BY price ASC,quantity DESC,name_en ASC";
        return $this->runDQL($query);
    }

    public function update() {
    
        
    }

    public function delete() {
        
        
    }

    public function getProductsBySub() {
        $query = "SELECT id,name_en,price,desc_en,image FROM `products` WHERE `status` = $this->status AND subcategory_id = '$this->subcategory_id' ORDER BY price ASC,quantity DESC,name_en ASC";
        return $this->runDQL($query);
    }

    public function searchOnProductId() {
        $query = "SELECT
                    `products`.*,
                    COUNT(`reviews`.`product_id`) AS `reviews_count`,
                    IF(
                        ROUND(AVG(`reviews`.`value`)) IS NULL,
                        0,
                        ROUND(AVG(`reviews`.`value`))
                    ) AS `reviews_avg`,
                    `subcategories`.`name_en` AS `subcategory_name_en`,
                    `brands`.`name_en` AS `brand_name_en`,
                    `categories`.`id` AS `category_id`,
                    `categories`.`name_en` AS `category_name_en`
                FROM
                    `products`
                LEFT JOIN `reviews` ON `products`.`id` = `reviews`.`product_id`
                JOIN `subcategories` ON `subcategories`.`id` = `products`.`subcategory_id`
                LEFT JOIN `brands` ON `brands`.`id` = `products`.`brand_id`
                LEFT JOIN `categories` ON `categories`.`id` = `subcategories`.`category_id`
                WHERE
                    `products`.`status` = $this->status AND `products`.`id` = $this->id 
                GROUP BY
                    `products`.`id`";
        return $this->runDQL($query);
    }

    public function getProductsByBrand() {
        $query = "  SELECT
                        `products`.`id`,
                        `products`.`name_en`,
                        `products`.`price`,
                        `products`.`desc_en`,
                        `products`.`image`
                    FROM
                        `products`
                    JOIN `brands` ON `brands`.`id` = `products`.`brand_id`
                    WHERE
                        `products`.`brand_id` = $this->brand_id AND `products`.`status` = $this->status";
            return $this->runDQL($query);
    }
}