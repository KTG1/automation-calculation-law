<?php
/**
 * Plugin Name: Settlement Calculator
 * Plugin URI:  https://github.com/KTG1/automation-calculation-law
 * Description: Creates a customizable settlement estimator page with tabbed FAQs.
 * Version:     1.1.0
 * Author:      KTG1
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: settlement-calculator
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'SC_VERSION', '1.1.0' );
define( 'SC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

function sc_default_settings() {
	return array(
		'kicker' => 'Claim planning tool', 'title' => 'Estimate what your settlement could leave you.', 'intro' => 'Build a clear estimate from damages to potential take-home amount. Your entries stay in this browser.',
		'economic_title' => 'Economic damages', 'medical_label' => 'Medical expenses', 'medical_help' => 'Past bills and treatment', 'future_medical_label' => 'Future medical costs', 'future_medical_help' => 'Expected care and rehabilitation', 'lost_income_label' => 'Lost income', 'lost_income_help' => 'Wages already missed', 'future_income_label' => 'Future lost earnings', 'future_income_help' => 'Reduced earning capacity', 'property_label' => 'Property damage', 'property_help' => 'Repair or replacement costs', 'other_label' => 'Other damages', 'other_help' => 'Travel and out-of-pocket costs',
		'adjustments_title' => 'Claim adjustments', 'multiplier_label' => 'Pain & suffering multiplier', 'multiplier_help' => 'Applied to medical expenses; 1× is modest and 5× is severe.', 'fault_label' => 'Your share of fault', 'fee_label' => 'Attorney fee', 'case_costs_label' => 'Case costs', 'case_costs_help' => 'Filing, experts, records', 'liens_label' => 'Medical liens', 'liens_help' => 'Amounts repaid from settlement', 'clear_label' => 'Clear all', 'auto_update_text' => 'Values update automatically.',
		'net_label' => 'Estimated take-home', 'economic_result_label' => 'Economic damages', 'non_economic_label' => 'Pain & suffering', 'gross_label' => 'Estimated claim value', 'fault_result_label' => 'Fault reduction', 'fee_result_label' => 'Attorney fee', 'deductions_label' => 'Costs & liens', 'disclaimer_title' => 'A planning estimate, not a promise.', 'disclaimer_text' => 'Laws, insurance limits, evidence, negotiations, taxes, and case-specific facts can materially change a settlement. This tool is not legal advice.',
		'faq_kicker' => 'Common questions', 'faq_title' => 'Understand the estimate.', 'faq_intro' => 'Explore how the calculator works, what may affect a claim, and what the estimate leaves out.',
		'faqs' => array(
			array( 'label' => 'Calculation basics', 'items' => array( array( 'question' => 'How is the estimated claim value calculated?', 'answer' => 'The calculator adds economic damages to an estimate for pain and suffering. The pain-and-suffering estimate applies your selected multiplier to past and future medical expenses.' ), array( 'question' => 'What does the likely range mean?', 'answer' => 'The range is 15% below and above the estimated take-home amount. It illustrates uncertainty and is not a prediction or guarantee.' ) ) ),
			array( 'label' => 'Costs & fees', 'items' => array( array( 'question' => 'When is the attorney fee deducted?', 'answer' => 'The calculator applies the entered attorney fee percentage after reducing the gross estimate for your share of fault.' ), array( 'question' => 'What should I include as case costs?', 'answer' => 'Examples can include filing fees, medical records, depositions, investigators, and expert witnesses. Fee arrangements vary.' ) ) ),
			array( 'label' => 'Legal context', 'items' => array( array( 'question' => 'Does this calculator provide legal advice?', 'answer' => 'No. It is a general planning tool. A qualified attorney can assess deadlines, evidence, insurance coverage, local law, and the facts of a specific matter.' ), array( 'question' => 'Can comparative fault eliminate recovery?', 'answer' => 'Rules differ by jurisdiction. Some systems reduce recovery by a percentage, while others can bar recovery at a threshold. The calculator only models a percentage reduction.' ) ) ),
		),
	);
}
function sc_get_settings() { $saved = get_option( 'sc_settings', array() ); return array_replace( sc_default_settings(), is_array( $saved ) ? $saved : array() ); }

function sc_activate_plugin() {
	$existing = get_page_by_path( 'settlement-calculator', OBJECT, 'page' );
	if ( $existing instanceof WP_Post ) { update_option( 'sc_page_id', $existing->ID ); return; }
	$page_id = wp_insert_post( array( 'post_title' => __( 'Settlement Calculator', 'settlement-calculator' ), 'post_name' => 'settlement-calculator', 'post_content' => '<!-- wp:shortcode -->[settlement_calculator]<!-- /wp:shortcode -->', 'post_status' => 'publish', 'post_type' => 'page' ), true );
	if ( ! is_wp_error( $page_id ) ) { update_option( 'sc_page_id', $page_id ); }
}
register_activation_hook( __FILE__, 'sc_activate_plugin' );
function sc_enqueue_assets() { wp_enqueue_style( 'settlement-calculator', SC_PLUGIN_URL . 'assets/css/settlement-calculator.css', array(), SC_VERSION ); wp_enqueue_script( 'settlement-calculator', SC_PLUGIN_URL . 'assets/js/settlement-calculator.js', array(), SC_VERSION, true ); }

function sc_render_calculator() {
	sc_enqueue_assets(); $s = sc_get_settings(); ob_start();
	?>
	<div class="sc-shell"><section aria-labelledby="sc-title">
	<header class="sc-intro"><p class="sc-kicker"><?php echo esc_html( $s['kicker'] ); ?></p><h1 id="sc-title"><?php echo esc_html( $s['title'] ); ?></h1><p><?php echo esc_html( $s['intro'] ); ?></p></header>
	<div class="sc-layout"><form class="sc-form" data-sc-form novalidate>
	<fieldset class="sc-section"><legend><span>1</span><?php echo esc_html( $s['economic_title'] ); ?></legend><div class="sc-grid">
	<?php sc_money_field( 'medical', $s['medical_label'], $s['medical_help'] ); sc_money_field( 'future-medical', $s['future_medical_label'], $s['future_medical_help'] ); sc_money_field( 'lost-income', $s['lost_income_label'], $s['lost_income_help'] ); sc_money_field( 'future-income', $s['future_income_label'], $s['future_income_help'] ); sc_money_field( 'property', $s['property_label'], $s['property_help'] ); sc_money_field( 'other', $s['other_label'], $s['other_help'] ); ?>
	</div></fieldset>
	<fieldset class="sc-section"><legend><span>2</span><?php echo esc_html( $s['adjustments_title'] ); ?></legend><div class="sc-grid">
	<label class="sc-field sc-field--wide" for="sc-multiplier"><span class="sc-label-row"><span><?php echo esc_html( $s['multiplier_label'] ); ?></span><output for="sc-multiplier" data-sc-multiplier-output>1.5×</output></span><input id="sc-multiplier" name="multiplier" type="range" min="1" max="5" step="0.1" value="1.5"><small><?php echo esc_html( $s['multiplier_help'] ); ?></small></label>
	<?php sc_percent_field( 'fault', $s['fault_label'], 0 ); sc_percent_field( 'fee', $s['fee_label'], 33.3 ); sc_money_field( 'case-costs', $s['case_costs_label'], $s['case_costs_help'] ); sc_money_field( 'liens', $s['liens_label'], $s['liens_help'] ); ?>
	</div></fieldset><div class="sc-actions"><button class="sc-reset" type="reset"><?php echo esc_html( $s['clear_label'] ); ?></button><p><?php echo esc_html( $s['auto_update_text'] ); ?></p></div></form>
	<aside class="sc-result" aria-labelledby="sc-result-title" aria-live="polite"><div class="sc-result-top"><p><?php echo esc_html( $s['net_label'] ); ?></p><h2 id="sc-result-title" data-sc-net>$0</h2><span data-sc-range>$0 – $0</span></div><dl class="sc-breakdown"><div><dt><?php echo esc_html( $s['economic_result_label'] ); ?></dt><dd data-sc-economic>$0</dd></div><div><dt><?php echo esc_html( $s['non_economic_label'] ); ?></dt><dd data-sc-non-economic>$0</dd></div><div><dt><?php echo esc_html( $s['gross_label'] ); ?></dt><dd data-sc-gross>$0</dd></div><div><dt><?php echo esc_html( $s['fault_result_label'] ); ?></dt><dd data-sc-fault>−$0</dd></div><div><dt><?php echo esc_html( $s['fee_result_label'] ); ?></dt><dd data-sc-fee>−$0</dd></div><div><dt><?php echo esc_html( $s['deductions_label'] ); ?></dt><dd data-sc-deductions>−$0</dd></div></dl><div class="sc-note"><strong><?php echo esc_html( $s['disclaimer_title'] ); ?></strong><p><?php echo esc_html( $s['disclaimer_text'] ); ?></p></div></aside></div></section>
	<?php sc_render_faqs( $s ); ?></div>
	<?php return (string) ob_get_clean();
}
add_shortcode( 'settlement_calculator', 'sc_render_calculator' );

function sc_render_faqs( $s ) {
	$groups = isset( $s['faqs'] ) && is_array( $s['faqs'] ) ? $s['faqs'] : array(); if ( empty( $groups ) ) return;
	?>
	<section class="sc-faq" aria-labelledby="sc-faq-title" data-sc-faq><header><p class="sc-kicker"><?php echo esc_html( $s['faq_kicker'] ); ?></p><h2 id="sc-faq-title"><?php echo esc_html( $s['faq_title'] ); ?></h2><p><?php echo esc_html( $s['faq_intro'] ); ?></p></header><div class="sc-faq-tabs" role="tablist" aria-label="<?php echo esc_attr( $s['faq_title'] ); ?>">
	<?php foreach ( $groups as $i => $group ) : ?><button type="button" role="tab" id="sc-faq-tab-<?php echo esc_attr( $i ); ?>" aria-controls="sc-faq-panel-<?php echo esc_attr( $i ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"><?php echo esc_html( $group['label'] ); ?></button><?php endforeach; ?></div>
	<?php foreach ( $groups as $i => $group ) : ?><div class="sc-faq-panel" role="tabpanel" id="sc-faq-panel-<?php echo esc_attr( $i ); ?>" aria-labelledby="sc-faq-tab-<?php echo esc_attr( $i ); ?>" <?php echo 0 === $i ? '' : 'hidden'; ?>><?php foreach ( $group['items'] as $item ) : ?><details><summary><?php echo esc_html( $item['question'] ); ?><span aria-hidden="true"></span></summary><div><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div></details><?php endforeach; ?></div><?php endforeach; ?></section>
	<?php
}
function sc_money_field( $name, $label, $help ) { $id = 'sc-' . sanitize_html_class( $name ); ?><label class="sc-field" for="<?php echo esc_attr( $id ); ?>"><span><?php echo esc_html( $label ); ?></span><span class="sc-input-wrap"><span aria-hidden="true">$</span><input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="number" min="0" step="100" inputmode="decimal" placeholder="0"></span><small><?php echo esc_html( $help ); ?></small></label><?php }
function sc_percent_field( $name, $label, $default ) { $id = 'sc-' . sanitize_html_class( $name ); ?><label class="sc-field" for="<?php echo esc_attr( $id ); ?>"><span><?php echo esc_html( $label ); ?></span><span class="sc-input-wrap sc-input-wrap--suffix"><input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="number" min="0" max="100" step="0.1" value="<?php echo esc_attr( $default ); ?>" inputmode="decimal"><span aria-hidden="true">%</span></span></label><?php }

function sc_admin_menu() { add_options_page( __( 'Settlement Calculator', 'settlement-calculator' ), __( 'Settlement Calculator', 'settlement-calculator' ), 'manage_options', 'settlement-calculator', 'sc_render_settings_page' ); }
add_action( 'admin_menu', 'sc_admin_menu' );
function sc_admin_assets( $hook ) { if ( 'settings_page_settlement-calculator' !== $hook ) return; wp_enqueue_style( 'settlement-calculator-admin', SC_PLUGIN_URL . 'assets/css/admin.css', array(), SC_VERSION ); wp_enqueue_script( 'settlement-calculator-admin', SC_PLUGIN_URL . 'assets/js/admin.js', array(), SC_VERSION, true ); }
add_action( 'admin_enqueue_scripts', 'sc_admin_assets' );

function sc_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) return; $s = sc_get_settings();
	$sections = array( 'Introduction' => array( 'kicker', 'title', 'intro' ), 'Economic damages' => array( 'economic_title', 'medical_label', 'medical_help', 'future_medical_label', 'future_medical_help', 'lost_income_label', 'lost_income_help', 'future_income_label', 'future_income_help', 'property_label', 'property_help', 'other_label', 'other_help' ), 'Claim adjustments' => array( 'adjustments_title', 'multiplier_label', 'multiplier_help', 'fault_label', 'fee_label', 'case_costs_label', 'case_costs_help', 'liens_label', 'liens_help', 'clear_label', 'auto_update_text' ), 'Results' => array( 'net_label', 'economic_result_label', 'non_economic_label', 'gross_label', 'fault_result_label', 'fee_result_label', 'deductions_label', 'disclaimer_title', 'disclaimer_text' ), 'FAQ introduction' => array( 'faq_kicker', 'faq_title', 'faq_intro' ) );
	?>
	<div class="wrap sc-admin"><h1><?php esc_html_e( 'Settlement Calculator', 'settlement-calculator' ); ?></h1><p><?php esc_html_e( 'Edit every public label and manage the tabbed FAQ content.', 'settlement-calculator' ); ?></p><?php if ( isset( $_GET['updated'] ) ) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'settlement-calculator' ); ?></p></div><?php endif; ?>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="sc_save_settings"><?php wp_nonce_field( 'sc_save_settings' ); ?>
	<?php foreach ( $sections as $heading => $keys ) : ?><section class="sc-admin-card"><h2><?php echo esc_html( $heading ); ?></h2><div class="sc-admin-fields"><?php foreach ( $keys as $key ) : ?><label><span><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></span><?php if ( in_array( $key, array( 'intro', 'disclaimer_text', 'faq_intro' ), true ) ) : ?><textarea name="settings[<?php echo esc_attr( $key ); ?>]" rows="3"><?php echo esc_textarea( $s[ $key ] ); ?></textarea><?php else : ?><input type="text" name="settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $s[ $key ] ); ?>"><?php endif; ?></label><?php endforeach; ?></div></section><?php endforeach; ?>
	<section class="sc-admin-card"><div class="sc-admin-heading"><div><h2><?php esc_html_e( 'FAQ tabs', 'settlement-calculator' ); ?></h2><p><?php esc_html_e( 'Each group becomes a tab.', 'settlement-calculator' ); ?></p></div><button class="button" type="button" data-add-group><?php esc_html_e( 'Add FAQ tab', 'settlement-calculator' ); ?></button></div><div data-faq-groups><?php foreach ( $s['faqs'] as $group_i => $group ) sc_admin_faq_group( $group, $group_i ); ?></div></section><?php submit_button( __( 'Save calculator settings', 'settlement-calculator' ) ); ?></form>
	<template data-group-template><?php sc_admin_faq_group( array( 'label' => '', 'items' => array( array( 'question' => '', 'answer' => '' ) ) ), '__GROUP__' ); ?></template><template data-item-template><?php sc_admin_faq_item( array( 'question' => '', 'answer' => '' ), '__GROUP__', '__ITEM__' ); ?></template></div>
	<?php
}
function sc_admin_faq_group( $group, $group_i ) { ?><div class="sc-faq-group" data-faq-group><div class="sc-faq-group-head"><label><span><?php esc_html_e( 'Tab label', 'settlement-calculator' ); ?></span><input type="text" name="settings[faqs][<?php echo esc_attr( $group_i ); ?>][label]" value="<?php echo esc_attr( $group['label'] ); ?>"></label><button class="button-link-delete" type="button" data-remove-group><?php esc_html_e( 'Remove tab', 'settlement-calculator' ); ?></button></div><div data-faq-items><?php foreach ( $group['items'] as $item_i => $item ) sc_admin_faq_item( $item, $group_i, $item_i ); ?></div><button class="button" type="button" data-add-item><?php esc_html_e( 'Add question', 'settlement-calculator' ); ?></button></div><?php }
function sc_admin_faq_item( $item, $group_i, $item_i ) { ?><div class="sc-faq-item" data-faq-item><label><span><?php esc_html_e( 'Question', 'settlement-calculator' ); ?></span><input type="text" name="settings[faqs][<?php echo esc_attr( $group_i ); ?>][items][<?php echo esc_attr( $item_i ); ?>][question]" value="<?php echo esc_attr( $item['question'] ); ?>"></label><label><span><?php esc_html_e( 'Answer', 'settlement-calculator' ); ?></span><textarea name="settings[faqs][<?php echo esc_attr( $group_i ); ?>][items][<?php echo esc_attr( $item_i ); ?>][answer]" rows="3"><?php echo esc_textarea( $item['answer'] ); ?></textarea></label><button class="button-link-delete" type="button" data-remove-item><?php esc_html_e( 'Remove question', 'settlement-calculator' ); ?></button></div><?php }

function sc_save_settings() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'You do not have permission to do that.', 'settlement-calculator' ) ); check_admin_referer( 'sc_save_settings' );
	$input = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array(); $defaults = sc_default_settings(); $clean = array();
	foreach ( $defaults as $key => $default ) { if ( 'faqs' !== $key ) $clean[ $key ] = isset( $input[ $key ] ) ? sanitize_textarea_field( $input[ $key ] ) : $default; }
	$clean['faqs'] = array(); if ( isset( $input['faqs'] ) && is_array( $input['faqs'] ) ) foreach ( $input['faqs'] as $group ) { if ( empty( $group['label'] ) ) continue; $clean_group = array( 'label' => sanitize_text_field( $group['label'] ), 'items' => array() ); if ( isset( $group['items'] ) && is_array( $group['items'] ) ) foreach ( $group['items'] as $item ) { if ( empty( $item['question'] ) && empty( $item['answer'] ) ) continue; $clean_group['items'][] = array( 'question' => sanitize_text_field( $item['question'] ), 'answer' => wp_kses_post( $item['answer'] ) ); } $clean['faqs'][] = $clean_group; }
	update_option( 'sc_settings', $clean, false ); wp_safe_redirect( add_query_arg( array( 'page' => 'settlement-calculator', 'updated' => '1' ), admin_url( 'options-general.php' ) ) ); exit;
}
add_action( 'admin_post_sc_save_settings', 'sc_save_settings' );
