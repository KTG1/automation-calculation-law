<?php
/**
 * Plugin Name: Settlement Calculator
 * Plugin URI:  https://github.com/KTG1/automation-calculation-law
 * Description: Creates a settlement estimator page with an accessible, responsive calculator.
 * Version:     1.0.0
 * Author:      KTG1
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: settlement-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_VERSION', '1.0.0' );
define( 'SC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Create the calculator page once when the plugin is activated.
 */
function sc_activate_plugin() {
	$existing_page = get_page_by_path( 'settlement-calculator', OBJECT, 'page' );

	if ( $existing_page instanceof WP_Post ) {
		update_option( 'sc_page_id', $existing_page->ID );
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => __( 'Settlement Calculator', 'settlement-calculator' ),
			'post_name'    => 'settlement-calculator',
			'post_content' => '<!-- wp:shortcode -->[settlement_calculator]<!-- /wp:shortcode -->',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		),
		true
	);

	if ( ! is_wp_error( $page_id ) ) {
		update_option( 'sc_page_id', $page_id );
	}
}
register_activation_hook( __FILE__, 'sc_activate_plugin' );

/**
 * Load assets only on pages where the calculator shortcode is rendered.
 */
function sc_enqueue_assets() {
	wp_enqueue_style(
		'settlement-calculator',
		SC_PLUGIN_URL . 'assets/css/settlement-calculator.css',
		array(),
		SC_VERSION
	);

	wp_enqueue_script(
		'settlement-calculator',
		SC_PLUGIN_URL . 'assets/js/settlement-calculator.js',
		array(),
		SC_VERSION,
		true
	);
}

/**
 * Render the calculator.
 *
 * @return string
 */
function sc_render_calculator() {
	sc_enqueue_assets();

	ob_start();
	?>
	<section class="sc-shell" aria-labelledby="sc-title">
		<header class="sc-intro">
			<p class="sc-kicker"><?php esc_html_e( 'Claim planning tool', 'settlement-calculator' ); ?></p>
			<h1 id="sc-title"><?php esc_html_e( 'Estimate what your settlement could leave you.', 'settlement-calculator' ); ?></h1>
			<p><?php esc_html_e( 'Build a clear estimate from damages to potential take-home amount. Your entries stay in this browser.', 'settlement-calculator' ); ?></p>
		</header>

		<div class="sc-layout">
			<form class="sc-form" data-sc-form novalidate>
				<fieldset class="sc-section">
					<legend><span>1</span><?php esc_html_e( 'Economic damages', 'settlement-calculator' ); ?></legend>
					<div class="sc-grid">
						<?php sc_money_field( 'medical', __( 'Medical expenses', 'settlement-calculator' ), __( 'Past bills and treatment', 'settlement-calculator' ) ); ?>
						<?php sc_money_field( 'future-medical', __( 'Future medical costs', 'settlement-calculator' ), __( 'Expected care and rehabilitation', 'settlement-calculator' ) ); ?>
						<?php sc_money_field( 'lost-income', __( 'Lost income', 'settlement-calculator' ), __( 'Wages already missed', 'settlement-calculator' ) ); ?>
						<?php sc_money_field( 'future-income', __( 'Future lost earnings', 'settlement-calculator' ), __( 'Reduced earning capacity', 'settlement-calculator' ) ); ?>
						<?php sc_money_field( 'property', __( 'Property damage', 'settlement-calculator' ), __( 'Repair or replacement costs', 'settlement-calculator' ) ); ?>
						<?php sc_money_field( 'other', __( 'Other damages', 'settlement-calculator' ), __( 'Travel and out-of-pocket costs', 'settlement-calculator' ) ); ?>
					</div>
				</fieldset>

				<fieldset class="sc-section">
					<legend><span>2</span><?php esc_html_e( 'Claim adjustments', 'settlement-calculator' ); ?></legend>
					<div class="sc-grid">
						<label class="sc-field sc-field--wide" for="sc-multiplier">
							<span class="sc-label-row"><span><?php esc_html_e( 'Pain & suffering multiplier', 'settlement-calculator' ); ?></span><output for="sc-multiplier" data-sc-multiplier-output>1.5×</output></span>
							<input id="sc-multiplier" name="multiplier" type="range" min="1" max="5" step="0.1" value="1.5">
							<small><?php esc_html_e( 'Applied to medical expenses; 1× is modest and 5× is severe.', 'settlement-calculator' ); ?></small>
						</label>
						<?php sc_percent_field( 'fault', __( 'Your share of fault', 'settlement-calculator' ), 0 ); ?>
						<?php sc_percent_field( 'fee', __( 'Attorney fee', 'settlement-calculator' ), 33.3 ); ?>
						<?php sc_money_field( 'case-costs', __( 'Case costs', 'settlement-calculator' ), __( 'Filing, experts, records', 'settlement-calculator' ) ); ?>
						<?php sc_money_field( 'liens', __( 'Medical liens', 'settlement-calculator' ), __( 'Amounts repaid from settlement', 'settlement-calculator' ) ); ?>
					</div>
				</fieldset>

				<div class="sc-actions">
					<button class="sc-reset" type="reset"><?php esc_html_e( 'Clear all', 'settlement-calculator' ); ?></button>
					<p><?php esc_html_e( 'Values update automatically.', 'settlement-calculator' ); ?></p>
				</div>
			</form>

			<aside class="sc-result" aria-labelledby="sc-result-title" aria-live="polite">
				<div class="sc-result-top">
					<p><?php esc_html_e( 'Estimated take-home', 'settlement-calculator' ); ?></p>
					<h2 id="sc-result-title" data-sc-net>$0</h2>
					<span data-sc-range>$0 – $0</span>
				</div>
				<dl class="sc-breakdown">
					<div><dt><?php esc_html_e( 'Economic damages', 'settlement-calculator' ); ?></dt><dd data-sc-economic>$0</dd></div>
					<div><dt><?php esc_html_e( 'Pain & suffering', 'settlement-calculator' ); ?></dt><dd data-sc-non-economic>$0</dd></div>
					<div><dt><?php esc_html_e( 'Estimated claim value', 'settlement-calculator' ); ?></dt><dd data-sc-gross>$0</dd></div>
					<div><dt><?php esc_html_e( 'Fault reduction', 'settlement-calculator' ); ?></dt><dd data-sc-fault>−$0</dd></div>
					<div><dt><?php esc_html_e( 'Attorney fee', 'settlement-calculator' ); ?></dt><dd data-sc-fee>−$0</dd></div>
					<div><dt><?php esc_html_e( 'Costs & liens', 'settlement-calculator' ); ?></dt><dd data-sc-deductions>−$0</dd></div>
				</dl>
				<div class="sc-note">
					<strong><?php esc_html_e( 'A planning estimate, not a promise.', 'settlement-calculator' ); ?></strong>
					<p><?php esc_html_e( 'Laws, insurance limits, evidence, negotiations, taxes, and case-specific facts can materially change a settlement. This tool is not legal advice.', 'settlement-calculator' ); ?></p>
				</div>
			</aside>
		</div>
	</section>
	<?php

	return (string) ob_get_clean();
}
add_shortcode( 'settlement_calculator', 'sc_render_calculator' );

/**
 * Render a currency field.
 *
 * @param string $name  Field name.
 * @param string $label Field label.
 * @param string $help  Helper text.
 */
function sc_money_field( $name, $label, $help ) {
	$id = 'sc-' . sanitize_html_class( $name );
	?>
	<label class="sc-field" for="<?php echo esc_attr( $id ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
		<span class="sc-input-wrap"><span aria-hidden="true">$</span><input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="number" min="0" step="100" inputmode="decimal" placeholder="0"></span>
		<small><?php echo esc_html( $help ); ?></small>
	</label>
	<?php
}

/**
 * Render a percentage field.
 *
 * @param string $name    Field name.
 * @param string $label   Field label.
 * @param float  $default Default value.
 */
function sc_percent_field( $name, $label, $default ) {
	$id = 'sc-' . sanitize_html_class( $name );
	?>
	<label class="sc-field" for="<?php echo esc_attr( $id ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
		<span class="sc-input-wrap sc-input-wrap--suffix"><input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="number" min="0" max="100" step="0.1" value="<?php echo esc_attr( $default ); ?>" inputmode="decimal"><span aria-hidden="true">%</span></span>
	</label>
	<?php
}
