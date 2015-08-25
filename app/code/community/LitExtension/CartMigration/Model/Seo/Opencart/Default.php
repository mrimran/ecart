<?php
/**
 * @project: CartMigration
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

class LitExtension_CartMigration_Model_Seo_Opencart_Default{

    public function getCategoriesExtQuery($cart, $categories){
        $categoryIds = $cart->duplicateFieldValueFromList($categories['object'], 'category_id');
        $parentIds = $cart->duplicateFieldValueFromList($categories['object'], 'parent_id');
        $allIds = array_merge($categoryIds, $parentIds);
        $category_ids_query = $this->_arrayToInConditionCategory($allIds);
        $parent_ids_query = $cart->arrayToInCondition($parentIds);
        $ext_rel_query = array(
            "url_alias" => "SELECT * FROM _DBPRF_url_alias WHERE query IN {$category_ids_query}",
            "category_parent" => "SELECT * FROM _DBPRF_category WHERE category_id IN {$parent_ids_query}"
        );
        return $ext_rel_query;
    }

    public function getCategoriesExtRelQuery($cart, $categories, $categoriesExt){
        $categoryIds = $cart->duplicateFieldValueFromList($categoriesExt['object']['category_parent'], 'parent_id');
        $category_ids_query = $this->_arrayToInConditionCategory($categoryIds);
        $ext_rel_query = array(
            "url_alias_2" => "SELECT * FROM _DBPRF_url_alias WHERE query IN {$category_ids_query}"
        );
        return $ext_rel_query;
    }

    public function convertCategorySeo($cart, $category, $categoriesExt){
        $result = array();
        $cat_desc = $cart->getRowFromListByField($categoriesExt['object']['url_alias'], 'query', 'category_id='.$category['category_id']);
        $notice = $cart->getNotice();
        $store_id = $notice['config']['languages'][$notice['config']['default_lang']];
        if($cat_desc){
            $path = $cat_desc['keyword'];
            if ($category['parent_id']) {
                $p1_desc = $cart->getRowValueFromListByField($categoriesExt['object']['url_alias'], 'query', 'category_id='.$category['parent_id'], 'keyword');
                if ($p1_desc) {
                    $path = $p1_desc . '/' . $path;
                    $p2_id = $cart->getRowValueFromListByField($categoriesExt['object']['category_parent'], 'category_id', $category['parent_id'], 'parent_id');
                    if ($p2_id) {
                        $p2_desc = $cart->getRowValueFromListByField($categoriesExt['object']['url_alias_2'], 'query', 'category_id=' . $p2_id, 'keyword');
                        if ($p2_desc) {
                            $path = $p2_desc . '/' . $path;
                        }
                    }
                }
            }
            $result[] = array(
                'store_id' => $store_id,
                'request_path' => $path
            );
            $this->_catUrlSuccess($cart, $category['category_id'], 0, $path, $notice['config']['cart_url']);
        }
        return $result;
    }

    public function getProductsExtQuery($cart, $products){
        return false;
    }

    public function getProductsExtRelQuery($cart, $products, $productsExt){
        $productIds = $cart->duplicateFieldValueFromList($products['object'], 'product_id');
        $product_ids_query = $this->_arrayToInConditionProduct($productIds);
        $ext_rel_query = array(
            "url_alias" => "SELECT * FROM _DBPRF_url_alias WHERE query IN {$product_ids_query}"
        );
        return $ext_rel_query;
    }

    public function convertProductSeo($cart, $product, $productsExt){
        $result = array();
        $pro_desc = $cart->getRowFromListByField($productsExt['object']['url_alias'], 'query', 'product_id='.$product['product_id']);
        $notice = $cart->getNotice();
        $store_id = $notice['config']['languages'][$notice['config']['default_lang']];
        if($pro_desc){
            $path = $pro_desc['keyword'];
            $result[] = array(
                'store_id' => $store_id,
                'request_path' => $path
            );
            $catParents = $cart->getListFromListByField($productsExt['object']['product_to_category'], 'product_id', $product['product_id']);
            foreach ($catParents as $cat) {
                $cart_url = $this->_getCatUrl($cart, $cat['category_id'], $notice['config']['cart_url']);
                if ($cart_url) {
                    $path_cat = $cart_url . '/' . $path;
                    $result[] = array(
                        'store_id' => $store_id,
                        'request_path' => $path_cat
                    );
                }
            }
        }
        return $result;
    }
    
    /**
     * Convert category's array to in condition in mysql query
     */
    protected function _arrayToInConditionCategory($array){
        if(empty($array)){
            return "('null')";
        }
        $result = "('category_id=".implode("','category_id=", $array)."')";
        return $result;
    }
    
    /**
     * Convert product's array to in condition in mysql query
     */
    protected function _arrayToInConditionProduct($array){
        if(empty($array)){
            return "('null')";
        }
        $result = "('product_id=".implode("','product_id=", $array)."')";
        return $result;
    }
    
    protected function _catUrlSuccess($cart, $id_import, $id_desc, $value = false, $cart_url) {
        return $this->_insertLeCaMgImport($cart, 'cat_url', $id_import, $id_desc, 1, $value, $cart_url);
    }

    protected function _getCatUrl($cart, $id_import, $cart_url) {
        $where = array(
            'domain' => $cart_url,
            'type' => 'cat_url',
            'id_import' => $id_import
        );
        $result = $cart->selectTableRow('lecamg/import', $where);
        if (!$result) {return false;}
        return $result['value'];
    }
    
    protected function _insertLeCaMgImport($cart, $type, $id_import, $mage_id, $status, $value = false, $cart_url){
        return $cart->insertTable('lecamg/import', array(
            'domain' => $cart_url,
            'type' => $type,
            'id_import' => $id_import,
            'mage_id' => $mage_id,
            'status' => $status,
            'value' => $value
        ));
    }
}