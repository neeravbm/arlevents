<?php
/**
 * Implements hook_views_data
 */

function act_event_views_data() {

  $table['arl_badge_scans'] = array(
    'table' => array(
      'group' => 'ARL Badge',
      'base' => array(
        'field' => 'scan_id', //Primary key
        'title' => 'ARL Badge',
        'help' => 'ARL Badge custom table'
      )
    ),
    'scan_id' => array(
      'title' => t('ARL Scan ID'),
      'help' => t('ARL badge Scan ID'),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_numeric'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      )
    ),
    'user_id' => array(
      'title' => t('ARL User ID'),
      'help' => t('ARL User ID '),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_string'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      )
    ),
    'scan_location' => array(
      'title' => t('ARL Scan location'),
      'help' => t('ARL scan location'),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_string'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      )
    ),
    'scan_date' => array(
      'title' => t('ARL Scan Date'),
      'help' => t('ARL Scan Date'),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_string'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      )
    ),
  );


  $table['arl_badge_userlist'] = array(

    'table' => array(
      'group' => 'ARL Badge User List',
      'base' => array(
        'field' => 'user_id', //Primary key
        'title' => 'ARL Badge User List',
        'help' => 'ARL Badge custom table'
      ),
      'join' => array(
        'arl_badge_scans' => array(
          'left_field' => 'user_id',
          'field' => 'user_id'
        ),
        'field_data_field_attendee_last_name' => array(
          'left_field' => 'user_id',
          'field' => 'user_id'
        ),
      ),
    ),
    'user_id' => array(
      'title' => t('ARL User ID'),
      'help' => t('ARL Badge Custom table user Id field'),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_numeric'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      )
    ),
    'cust_key' => array(
      'title' => t('ARL Cust Key'),
      'help' => t('ARL Cust Key'),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_string'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      ),
      'relationship' => array(
        'handler' => 'views_handler_relationship',
        'label' => t('CustomerKey Relation'),
        'base' => 'field_data_field_customer_key',
        'base field' => 'field_customer_key_value',
        'relationship field' => 'cust_key',

      ),
    ),
    'evt_key' => array(
      'title' => t('ARL Event Key'),
      'help' => t('ARL Event Key'),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_string'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      ),
      'argument' => array(
        'handler' => 'views_handler_argument_string',
      ),
    ),
    'print_date' => array(
      'title' => t('ARL Print Date'),
      'help' => t('ARL User Badge Print date'),
      'field' => array(
        'click sortable' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_string'
      ),
      'sort' => array(
        'handler' => 'views_handler_sort'
      )
    ),
  );


  return $table;
}


function act_event_views_data_alter(&$data) {
  //dsm($data['field_data_field_customer_key']);
}
