<?php 
class ModelCatalogVldiscount extends Model
{
    public function getDiscountDetails($product_info) {
        $discounts = $this->model_catalog_product->getProductDiscounts($product_info['product_id']);

        $discountDetails = array();

        foreach ($discounts as $discount) {
            $discountDetails[] = array(
                'quantity' => $discount['quantity'],
                'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
            );
        }

        return $discountDetails;
    }
}