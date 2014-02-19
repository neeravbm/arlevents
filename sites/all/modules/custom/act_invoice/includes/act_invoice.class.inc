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
 * Function to create/update while user login
 */
public function act_invoice_user_invoice($account = NULL){
  global $user;
  module_load_include('php', 'avectra', 'includes/ACT');
  $act = new ACT(SOAPURL, XWEBUSER, XWEBPASS);
  $node = null;
  $org_key = '';
  
  if (empty($account)) {
		$account = user_load($user->uid);
	}
  
  if(function_exists('act_event_user_is_poc')){
	  if(!act_event_user_is_poc($account->uid)){
		  return;
		}	
	}
	
  $customer_key = field_get_items('user',$account,'field_customer_key');
  $customer_key = $customer_key[0]['value'];
  
  $field_organization = field_get_items('user',$account,'field_organization');
  $field_organization = $field_organization[0]['target_id'];
  
  if($field_organization){
	  $org_key = $this->get_user_org_key($field_organization);	
	}
  
  $invoices = array();
  if (!empty($org_key) && !empty($customer_key)) {
    //$invoices = $act->getActiveInvoicesForCustomer($customer_key, $org_key);
    $invoices = $act->getActiveInvoicesForCustomerOrganization($customer_key);
  }
  //debug($invoices);
  // Create Invoice product
  if (!empty($invoices)) {
	  foreach($invoices as $invoice){
			
			$invoice_key = (string) $invoice->inv_key;
			$inv_date =  (string) $invoice->inv_date;
			$inv_balance = (string) $invoice->inv_balance;
			$node = $this->invoice_already_exist($invoice_key);
			
			if (empty($node)) {
        $node = new stdClass();
        $node->type = 'invoice';
        $node->status = 1;
        $node->promote = 1;
        $node->sticky = 0;
        $node->language = LANGUAGE_NONE;
        $node->uid = $account->uid;
        $node->path['pathauto'] = TRUE;
        $node->field_invoice_key[LANGUAGE_NONE][0]['value'] = $invoice_key;
        $node->title = t('Invoice Dated @date For User @uid', array('@date' => $inv_date, '@uid' => $account->uid ));
        $values = array(
					'title' => t('Invoice Dated @date For User @uid', array('@date' => $inv_date, '@uid' => $user->uid )),
					'sku' => 'INVOICE'.rand(1,10000),
					'amount' => $inv_balance,
					'uid' => $account->uid,
			  );
	
				$pid = $this->act_invoice_create_product('invoice',$values);
				$node->field_invoice_date[LANGUAGE_NONE][0]['value'] = format_date(strtotime($inv_date), 'custom', 'Y-m-d H:i:s', TIMEZONE); //12/03/2013
				$node->field_product[LANGUAGE_NONE][0]['product_id'] = $pid;
				node_save($node);
      }
		}
		
    watchdog('invoice_node', $node->nid);
	}
	else {
		watchdog('invoice_error', 'node not created');
	}
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


