<?php

namespace Posti_Warehouse;

defined('ABSPATH') || exit;

class Posti_Warehouse_Debug {
	
	private $is_test = false;
	
	public function __construct( array &$options) {
		$this->is_test = Posti_Warehouse_Settings::is_test($options);
		add_action('admin_menu', array($this, 'posti_wh_debug_page'));
	}
	
	public function posti_wh_debug_page() {
		add_submenu_page(
				'options-general.php',
				Posti_Warehouse_Text::field_warehouse_debug(),
				Posti_Warehouse_Text::field_warehouse_debug(),
				'manage_options',
				'posti_wh_debug',
				array($this, 'posti_wh_debug_page_html')
		);
	}
	
	public function posti_wh_debug_page_html() {
		if (!current_user_can('manage_options')) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<?php $token_data = get_option('posti_wh_api_auth'); ?>
			<?php if (is_array($token_data)) : ?>
				<div class="notice notice-info">
					<p style = "word-break: break-all;"><strong><?php echo esc_html(Posti_Warehouse_Text::logs_token_data()); ?><br/> </strong> <?php echo esc_html($token_data['token']); ?></p>
					<p><strong><?php echo esc_html(Posti_Warehouse_Text::logs_token_expiration()); ?> </strong> <?php echo esc_html(date('Y-m-d H:i:s'), esc_html($token_data['expires'])); ?></p>
				</div>
			<?php endif; ?>
			<?php
			$logger = new Posti_Warehouse_Logger();
			$logs = $logger->getLogs();
			?>
			<?php if (count($logs)) : ?>
				<h3><?php echo esc_html(Posti_Warehouse_Text::logs_title()); ?></h3>
				<table class="widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th class="manage-column column-columnname " style = "width: 150px" scope="col"><?php echo esc_html(Posti_Warehouse_Text::column_created_date()); ?></th> 
							<th class="manage-column column-columnname" style = "width: 80px" scope="col"><?php echo esc_html(Posti_Warehouse_Text::column_type()); ?></th>
							<th class="manage-column column-columnname " scope="col"><?php echo esc_html(Posti_Warehouse_Text::column_message()); ?></th> 
						</tr>
					</thead>
					<tbody>
						<?php foreach ($logs as $key => $log) : ?>
							<tr class="<?php echo ( 0 == $key % 2 ? 'alternate' : '' ); ?>">
								<td class="column-columnname"><?php echo esc_html($log->created_at); ?></td>
								<td class="column-columnname"><?php echo esc_html($log->type); ?></td>
								<td class="column-columnname"><?php echo nl2br(esc_html($log->message)); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<h3><?php echo esc_html(Posti_Warehouse_Text::logs_empty()); ?></h3>
			<?php endif; ?>
		</div>
		<?php
	}
}
