<?php
class ModelCatalogVlproductprices extends Model
{
    public function getProductPrices($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vlproductprices WHERE product_id = '" . (int)$product_id . "' ORDER BY price");

        return $query->rows;
    }
}
