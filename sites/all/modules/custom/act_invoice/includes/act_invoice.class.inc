<?php

/**
 * @file
 * Invoice class.
 */

class ActInvoice {
	private $uid;
	private $invoice_key;
  
	
 /*
 * function to check if invoice key already exist in node table.
 * @para $invoice_key
 *   invoice key
 * @return $node
 *   node object
 */
public function invoice_already_exist($invoice_key){
	$query = db_select('node', 'node');
	$query->join('field_data_field_invoice_key', 'invoice', 'invoice.entity_id = node.nid');
	$query->condition('invoice.field_invoice_key_value', $invoice_key)
				->fields('node', array('nid'));
	$result = $query->execute()->fetchCol();
	$node = count($result) > 0 ? node_load($result[0]) : array();
	return $node;	
}



/*
 * Function to create products
 * @para $values
 *   associative array contains key and value
 * @return product_id
 *   product id
 */
public function act_invoice_create_product($product_type,$values) {
	$form_state = array();
	$form_state['values'] = array();
	$form = array();
	$form['#parents'] = array();

	// Generate a new product object
	$product = commerce_product_new($product_type);

	$product->status = 1;
	$product->uid = $values['uid'];

	$product->sku = $values['sku'];
	$product->title = $values['title'];
	$product->created = $product->changed = time();

	$price = array(LANGUAGE_NONE => array(0 => array(
				'amount' => $values['amount'] * 100, 
				'currency_code' => commerce_default_currency(),
			)));
	$form_state['values']['commerce_price'] = $price;

	// Notify field widgets to save their field data
	field_attach_submit('commerce_product', $product, $form, $form_state);

	commerce_product_save($product);
	return $product->product_id;
}

	
/*
 * function get organization key 
 */
public function get_user_org_key($org_id){
	$org_key = '';
	$query = db_select('node ','node');
	$query->fields('org_key', array('field_organization_key_value'));
	$query->join('field_data_field_organization_key ','org_key', 'node.nid = org_key.entity_id');
	$query->condition('node.status', 1, '=');
	$query->condition('node.type', array('organization'), 'IN');
	$query->condition('node.nid', $org_id, '=');
	$org_key = $query->execute()->fetchField();
return $org_key; 
}


} // class end here


