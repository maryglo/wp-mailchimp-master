<?php
$base = dirname(dirname(__FILE__));
$baseDir = dirname($base);
return array(
    'Wp_Mailchimp_Master_API_v3' => $baseDir . '/includes/api/class-api-v3.php',
    'WP_MCM_API_Connection_Exception' => $baseDir . '/includes/api/class-connection-exception.php',
    'Wp_Mcm_Exception' => $baseDir . '/includes/api/class-wp-mcm-exception.php',
    'Wp_Mailchimp_Master_Resource_Not_Found_Exception' => $baseDir . '/includes/api/class-resource-not-found-exception.php',
    'Wp_Mailchimp_Master_v3_Client' => $baseDir . '/includes/api/class-api-v3-client.php',
    'Wp_MCM_Admin_Messages' => $baseDir . '/includes/class-wp-mcm-admin-messages.php'
);