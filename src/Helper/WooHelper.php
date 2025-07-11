<?php

namespace VnCoder\Helper;

class WooHelper
{
    protected string $api_url, $api_key, $api_secret;

    function __construct($api_url, $api_key, $api_secret){
        $this->api_url = $api_url;
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    public function submitProduct($name, $description, $content, $photo = "", $category = "", $price = 0){
        $woocommerce = $this->initWoo();
        $productData = [
            'name' => $name,
            'type' => 'simple',
            'short_description' => $description,
            'description' => $content
        ];

        if($photo){
            $productData['images'] = [
                [
                    'src' => $photo,
                    'name' => $name,
                    'alt' => $description
                ]
            ];
        }


        if($price > 0){
            $productData['regular_price'] = $price;
        }
        if($category){
            $categoryData = explode(',', $category);
            foreach ($categoryData as $cat) {
                $categoryId = $this->getCategoryID($cat);
                if($categoryId) $productData['categories'][] = ['id' => $categoryId];
            }
        }

        return $woocommerce->post('products', $productData);
    }

    public function getCategoryID($category){
        $woocommerce = $this->initWoo();
        $categoryData = [
            'create' => [
                [
                    'name' => trim($category)
                ]
            ]
        ];

        $updateCategory = $woocommerce->post('products/categories/batch', $categoryData);
        $updateInfo = isset($updateCategory->create) ? $updateCategory->create[0] : null;
        if($updateInfo){
            if(isset($updateInfo->error)){
                return $updateInfo->error->data->resource_id;
            }else{
                return $updateInfo->id;
            }
        }
        return null;
    }

    protected function initWoo(){
        if(!class_exists('Automattic\WooCommerce\Client')){
            return false;
        }

        return new \Automattic\WooCommerce\Client(
            $this->api_url,
            $this->api_key,
            $this->api_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false,
                'timeout' => 30
            ]
        );
    }

}