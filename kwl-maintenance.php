<?php
/**
 * Plugin Name: KWL Maintenance Mode
 * Plugin URI:  https://kwlhub.com
 * Description: A fully customizable maintenance/under-construction page. Edit your company name, message, progress bar, contact links, colors, and more — right from the WordPress dashboard.
 * Version:     1.0.0
 * Author:      KWL Hub
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'KWL_MAINT_VERSION', '1.0.0' );
define( 'KWL_MAINT_DIR',     plugin_dir_path( __FILE__ ) );
define( 'KWL_MAINT_URL',     plugin_dir_url( __FILE__ ) );
define( 'KWL_MAINT_OPTIONS', 'kwl_maintenance_options' );

/* ---------------------------------------------------------------
   DEFAULT SETTINGS
--------------------------------------------------------------- */
function kwl_maint_defaults() {
    return [
        // Toggle
        'enabled'            => '0',

        // Content
        'site_name'          => get_bloginfo( 'name' ) ?: 'KWL Hub',
        'tagline'            => "We're building something great! Check back soon.",
        'support_note'       => 'Site under construction. Exciting updates coming soon!',
        'eta_text'           => 'Estimated completion: <strong>in progress</strong> &nbsp;•&nbsp; Construction in progress',
        'status_badge_text'  => 'Our team is setting things up',
        'footer_note'        => 'Thanks for your patience',

        // Progress bar
        'progress_value'     => '68',   // 0–100
        'show_progress'      => '1',

        // Contact links
        'support_email'      => 'support@kwlhub.com',
        'facebook_url'       => 'https://www.facebook.com/KWLHub',
        'show_email_link'    => '1',
        'show_fb_link'       => '1',
        'show_status_btn'    => '1',

        // Colors
        'color_bg_from'      => '#f5f7fc',
        'color_bg_to'        => '#eef2f8',
        'color_card_bg'      => '#ffffff',
        'color_title'        => '#1a2a3f',
        'color_title_to'     => '#2c4c7c',
        'color_body_text'    => '#2c3e50',
        'color_progress_from'=> '#2c7cb6',
        'color_progress_to'  => '#4f9fda',
        'color_icon'         => '#2c3e66',
        'color_link'         => '#3f6b9e',
        'color_link_bg'      => '#f0f4fa',

        // Bypass roles
        'bypass_admins'      => '1',
        'bypass_editors'     => '0',

        // SEO / meta
        'meta_robots'        => 'noindex, nofollow',
    ];
}

/* ---------------------------------------------------------------
   GET OPTIONS (merged with defaults)
--------------------------------------------------------------- */
function kwl_maint_options() {
    return wp_parse_args( get_option( KWL_MAINT_OPTIONS, [] ), kwl_maint_defaults() );
}

/* ---------------------------------------------------------------
   ACTIVATION — save defaults if no option exists yet
--------------------------------------------------------------- */
register_activation_hook( __FILE__, function() {
    if ( false === get_option( KWL_MAINT_OPTIONS ) ) {
        add_option( KWL_MAINT_OPTIONS, kwl_maint_defaults() );
    }
});

/* ---------------------------------------------------------------
   ADMIN MENU
--------------------------------------------------------------- */
add_action( 'admin_menu', function() {
    add_options_page(
        'KWL Maintenance',
        'KWL Maintenance',
        'manage_options',
        'kwl-maintenance',
        'kwl_maint_settings_page'
    );
});

/* ---------------------------------------------------------------
   REGISTER SETTINGS
--------------------------------------------------------------- */
add_action( 'admin_init', function() {
    register_setting( 'kwl_maintenance_group', KWL_MAINT_OPTIONS, [
        'sanitize_callback' => 'kwl_maint_sanitize',
    ]);
});

function kwl_maint_sanitize( $input ) {
    $defaults = kwl_maint_defaults();
    $clean    = [];

    $text_fields = [
        'site_name','tagline','support_note','eta_text','status_badge_text',
        'footer_note','support_email','facebook_url','meta_robots',
    ];
    $int_fields  = [ 'progress_value' ];
    $hex_fields  = [
        'color_bg_from','color_bg_to','color_card_bg','color_title','color_title_to',
        'color_body_text','color_progress_from','color_progress_to',
        'color_icon','color_link','color_link_bg',
    ];
    $bool_fields = [
        'enabled','show_progress','show_email_link','show_fb_link',
        'show_status_btn','bypass_admins','bypass_editors',
    ];

    foreach ( $text_fields as $f ) {
        $clean[ $f ] = isset( $input[ $f ] ) ? wp_kses_post( $input[ $f ] ) : $defaults[ $f ];
    }
    foreach ( $int_fields as $f ) {
        $v = isset( $input[ $f ] ) ? intval( $input[ $f ] ) : intval( $defaults[ $f ] );
        $clean[ $f ] = max( 0, min( 100, $v ) );
    }
    foreach ( $hex_fields as $f ) {
        $v = isset( $input[ $f ] ) ? sanitize_hex_color( $input[ $f ] ) : $defaults[ $f ];
        $clean[ $f ] = $v ?: $defaults[ $f ];
    }
    foreach ( $bool_fields as $f ) {
        $clean[ $f ] = isset( $input[ $f ] ) && $input[ $f ] ? '1' : '0';
    }

    return $clean;
}

/* ---------------------------------------------------------------
   ADMIN SETTINGS PAGE
--------------------------------------------------------------- */
function kwl_maint_settings_page() {
    $opts = kwl_maint_options();
    $active = $opts['enabled'] === '1';
    ?>
    <div class="wrap kwl-admin-wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            🔧 KWL Maintenance Mode
            <span class="kwl-status-pill <?php echo $active ? 'active' : 'inactive'; ?>">
                <?php echo $active ? '● ACTIVE' : '○ INACTIVE'; ?>
            </span>
        </h1>
        <p class="kwl-desc">Customize your maintenance page below, then enable it when ready.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'kwl_maintenance_group' ); ?>

            <div class="kwl-tabs">
                <button type="button" class="kwl-tab active" data-tab="general">General</button>
                <button type="button" class="kwl-tab" data-tab="content">Content & Text</button>
                <button type="button" class="kwl-tab" data-tab="colors">Colors</button>
                <button type="button" class="kwl-tab" data-tab="links">Contact Links</button>
                <button type="button" class="kwl-tab" data-tab="access">Access & SEO</button>
            </div>

            <!-- ===== GENERAL TAB ===== -->
            <div class="kwl-panel active" id="tab-general">
                <div class="kwl-card">
                    <h2>🚦 Maintenance Mode Toggle</h2>
                    <label class="kwl-toggle-label">
                        <div class="kwl-toggle-switch">
                            <input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[enabled]" value="1" <?php checked( $opts['enabled'], '1' ); ?>>
                            <span class="kwl-slider"></span>
                        </div>
                        <span><?php echo $active ? '<strong>Maintenance mode is ON.</strong> Visitors see the maintenance page.' : '<strong>Maintenance mode is OFF.</strong> Site is live.'; ?></span>
                    </label>
                </div>

                <div class="kwl-card">
                    <h2>🏢 Site Identity</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Company / Site Name</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[site_name]" value="<?php echo esc_attr( $opts['site_name'] ); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                </div>

                <div class="kwl-card">
                    <h2>📊 Progress Bar</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Show Progress Bar</th>
                            <td><label class="kwl-toggle-label small">
                                <div class="kwl-toggle-switch small">
                                    <input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_progress]" value="1" <?php checked( $opts['show_progress'], '1' ); ?>>
                                    <span class="kwl-slider"></span>
                                </div>
                            </label></td>
                        </tr>
                        <tr>
                            <th>Progress % <span class="kwl-hint">(0–100)</span></th>
                            <td>
                                <input type="range" min="0" max="100" name="<?php echo KWL_MAINT_OPTIONS; ?>[progress_value]" value="<?php echo esc_attr( $opts['progress_value'] ); ?>" class="kwl-range" oninput="document.getElementById('kwl-prog-val').textContent=this.value+'%'">
                                <span id="kwl-prog-val" class="kwl-range-val"><?php echo esc_html( $opts['progress_value'] ); ?>%</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ===== CONTENT TAB ===== -->
            <div class="kwl-panel" id="tab-content">
                <div class="kwl-card">
                    <h2>✏️ Page Text</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Main Message <span class="kwl-hint">(under the title)</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[tagline]" value="<?php echo esc_attr( $opts['tagline'] ); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>ETA / Status Text <span class="kwl-hint">(supports basic HTML)</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[eta_text]" value="<?php echo esc_attr( $opts['eta_text'] ); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Status Badge Text</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[status_badge_text]" value="<?php echo esc_attr( $opts['status_badge_text'] ); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Support Note <span class="kwl-hint">(bottom of card)</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[support_note]" value="<?php echo esc_attr( $opts['support_note'] ); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Footer Text</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[footer_note]" value="<?php echo esc_attr( $opts['footer_note'] ); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ===== COLORS TAB ===== -->
            <div class="kwl-panel" id="tab-colors">
                <div class="kwl-card">
                    <h2>🎨 Color Customization</h2>
                    <table class="form-table kwl-form-table">
                        <?php
                        $color_fields = [
                            'color_bg_from'       => 'Background Gradient — Start',
                            'color_bg_to'         => 'Background Gradient — End',
                            'color_card_bg'       => 'Card Background',
                            'color_title'         => 'Title Gradient — Start',
                            'color_title_to'      => 'Title Gradient — End',
                            'color_body_text'     => 'Body Text',
                            'color_progress_from' => 'Progress Bar — Start',
                            'color_progress_to'   => 'Progress Bar — End',
                            'color_icon'          => 'Icon Color',
                            'color_link'          => 'Button Text Color',
                            'color_link_bg'       => 'Button Background',
                        ];
                        foreach ( $color_fields as $key => $label ) : ?>
                        <tr>
                            <th><?php echo esc_html( $label ); ?></th>
                            <td>
                                <input type="color" name="<?php echo KWL_MAINT_OPTIONS; ?>[<?php echo $key; ?>]" value="<?php echo esc_attr( $opts[ $key ] ); ?>" class="kwl-color-input">
                                <input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[<?php echo $key; ?>]_text" value="<?php echo esc_attr( $opts[ $key ] ); ?>" class="kwl-hex-text" readonly>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- ===== LINKS TAB ===== -->
            <div class="kwl-panel" id="tab-links">
                <div class="kwl-card">
                    <h2>🔗 Contact & Social Links</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Support Email</th>
                            <td><input type="email" name="<?php echo KWL_MAINT_OPTIONS; ?>[support_email]" value="<?php echo esc_attr( $opts['support_email'] ); ?>" class="regular-text">
                            <label class="kwl-inline-check"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_email_link]" value="1" <?php checked( $opts['show_email_link'], '1' ); ?>> Show link</label>
                            </td>
                        </tr>
                        <tr>
                            <th>Facebook Page URL</th>
                            <td><input type="url" name="<?php echo KWL_MAINT_OPTIONS; ?>[facebook_url]" value="<?php echo esc_attr( $opts['facebook_url'] ); ?>" class="regular-text">
                            <label class="kwl-inline-check"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_fb_link]" value="1" <?php checked( $opts['show_fb_link'], '1' ); ?>> Show link</label>
                            </td>
                        </tr>
                        <tr>
                            <th>"Check Status" Button</th>
                            <td><label class="kwl-inline-check"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_status_btn]" value="1" <?php checked( $opts['show_status_btn'], '1' ); ?>> Show button</label></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ===== ACCESS & SEO TAB ===== -->
            <div class="kwl-panel" id="tab-access">
                <div class="kwl-card">
                    <h2>🔐 Who Can Bypass the Maintenance Page?</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Administrators</th>
                            <td><label><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[bypass_admins]" value="1" <?php checked( $opts['bypass_admins'], '1' ); ?>> Admins always see the real site</label></td>
                        </tr>
                        <tr>
                            <th>Editors</th>
                            <td><label><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[bypass_editors]" value="1" <?php checked( $opts['bypass_editors'], '1' ); ?>> Editors can bypass too</label></td>
                        </tr>
                    </table>
                </div>
                <div class="kwl-card">
                    <h2>🤖 SEO / Robots Meta</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Robots Meta Tag</th>
                            <td>
                                <select name="<?php echo KWL_MAINT_OPTIONS; ?>[meta_robots]">
                                    <option value="noindex, nofollow" <?php selected( $opts['meta_robots'], 'noindex, nofollow' ); ?>>noindex, nofollow (recommended)</option>
                                    <option value="noindex, follow" <?php selected( $opts['meta_robots'], 'noindex, follow' ); ?>>noindex, follow</option>
                                    <option value="index, follow" <?php selected( $opts['meta_robots'], 'index, follow' ); ?>>index, follow</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style="margin-top:24px;">
                <?php submit_button( 'Save Settings', 'primary large' ); ?>
                <a href="<?php echo esc_url( add_query_arg( 'kwl_preview', '1', home_url('/') ) ); ?>" target="_blank" class="button button-secondary button-large" style="margin-left:12px;">👁 Preview Maintenance Page</a>
            </div>
        </form>
    </div>

    <style>
    .kwl-admin-wrap { max-width: 900px; }
    .kwl-desc { color: #666; margin-bottom: 20px; }
    .kwl-status-pill { font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 600; }
    .kwl-status-pill.active { background: #d4edda; color: #155724; }
    .kwl-status-pill.inactive { background: #e2e3e5; color: #383d41; }

    .kwl-tabs { display: flex; gap: 4px; border-bottom: 2px solid #e0e0e0; margin-bottom: 20px; flex-wrap: wrap; }
    .kwl-tab { background: none; border: none; padding: 10px 18px; cursor: pointer; font-size: 13px; font-weight: 500; color: #555; border-bottom: 2px solid transparent; margin-bottom: -2px; border-radius: 4px 4px 0 0; transition: all .15s; }
    .kwl-tab:hover { background: #f5f5f5; color: #1d2327; }
    .kwl-tab.active { background: #fff; color: #2271b1; border-bottom-color: #2271b1; border: 1px solid #e0e0e0; border-bottom: 2px solid #fff; }

    .kwl-panel { display: none; }
    .kwl-panel.active { display: block; }

    .kwl-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px 24px; margin-bottom: 16px; }
    .kwl-card h2 { font-size: 15px; margin: 0 0 16px; padding: 0; border: none; }
    .kwl-form-table th { width: 260px; padding: 10px 0; font-weight: 500; vertical-align: middle; }
    .kwl-form-table td { padding: 8px 0; vertical-align: middle; }
    .kwl-hint { font-weight: 400; color: #888; font-size: 11px; }

    .kwl-toggle-label { display: flex; align-items: center; gap: 12px; cursor: pointer; }
    .kwl-toggle-label.small { gap: 8px; }
    .kwl-toggle-switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
    .kwl-toggle-switch.small { width: 36px; height: 20px; }
    .kwl-toggle-switch input { opacity: 0; width: 0; height: 0; }
    .kwl-slider { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #ccc; border-radius: 24px; transition: .3s; cursor: pointer; }
    .kwl-slider:before { content: ''; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: .3s; }
    .kwl-toggle-switch.small .kwl-slider:before { height: 14px; width: 14px; }
    input:checked + .kwl-slider { background: #2271b1; }
    input:checked + .kwl-slider:before { transform: translateX(20px); }
    .kwl-toggle-switch.small input:checked + .kwl-slider:before { transform: translateX(16px); }

    .kwl-range { width: 220px; vertical-align: middle; }
    .kwl-range-val { display: inline-block; min-width: 36px; font-weight: 600; color: #2271b1; margin-left: 8px; }

    .kwl-color-input { width: 44px; height: 32px; border: 1px solid #ddd; border-radius: 4px; padding: 2px; cursor: pointer; vertical-align: middle; }
    .kwl-hex-text { width: 80px; margin-left: 8px; font-family: monospace; font-size: 12px; vertical-align: middle; }
    .kwl-inline-check { margin-left: 10px; font-size: 13px; color: #555; }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        document.querySelectorAll('.kwl-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.kwl-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.kwl-panel').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
            });
        });

        // Sync color picker → hex text
        document.querySelectorAll('.kwl-color-input').forEach(function(picker) {
            const hexField = picker.nextElementSibling;
            picker.addEventListener('input', function() {
                hexField.value = picker.value;
                // also update the real hidden input name
                picker.name = picker.name.replace('_text', '');
            });
        });
    });
    </script>
    <?php
}

/* ---------------------------------------------------------------
   FRONT-END INTERCEPT
--------------------------------------------------------------- */
add_action( 'template_redirect', function() {
    $opts = kwl_maint_options();

    if ( $opts['enabled'] !== '1' ) {
        // Allow preview for admins
        if ( isset( $_GET['kwl_preview'] ) && current_user_can('manage_options') ) {
            kwl_maint_render_page( $opts );
            exit;
        }
        return;
    }

    // Bypass checks
    if ( is_user_logged_in() ) {
        if ( $opts['bypass_admins'] === '1' && current_user_can('manage_options') ) return;
        if ( $opts['bypass_editors'] === '1' && current_user_can('edit_others_posts') ) return;
    }

    // Skip WP login/admin pages
    if ( is_admin() || $GLOBALS['pagenow'] === 'wp-login.php' ) return;

    kwl_maint_render_page( $opts );
    exit;
});

/* ---------------------------------------------------------------
   RENDER THE MAINTENANCE PAGE
--------------------------------------------------------------- */
function kwl_maint_render_page( $opts ) {
    $progress = intval( $opts['progress_value'] );
    $p_min    = max( 0, $progress - 5 );
    $p_max    = min( 100, $progress + 6 );

    // Build animation keyframes based on progress value
    $kf_start = $progress . '%';
    $kf_mid   = $p_max . '%';
    $kf_end   = $p_min . '%';

    status_header(503);
    header( 'Retry-After: 3600' );
    header( 'Content-Type: text/html; charset=UTF-8' );
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <meta name="robots" content="<?php echo esc_attr( $opts['meta_robots'] ); ?>">
    <title><?php echo esc_html( $opts['site_name'] ); ?> &bull; Under Construction</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800|Raleway:300,400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --bg-from:       <?php echo esc_attr( $opts['color_bg_from'] ); ?>;
            --bg-to:         <?php echo esc_attr( $opts['color_bg_to'] ); ?>;
            --card-bg:       <?php echo esc_attr( $opts['color_card_bg'] ); ?>;
            --title-from:    <?php echo esc_attr( $opts['color_title'] ); ?>;
            --title-to:      <?php echo esc_attr( $opts['color_title_to'] ); ?>;
            --body-text:     <?php echo esc_attr( $opts['color_body_text'] ); ?>;
            --prog-from:     <?php echo esc_attr( $opts['color_progress_from'] ); ?>;
            --prog-to:       <?php echo esc_attr( $opts['color_progress_to'] ); ?>;
            --icon-color:    <?php echo esc_attr( $opts['color_icon'] ); ?>;
            --link-text:     <?php echo esc_attr( $opts['color_link'] ); ?>;
            --link-bg:       <?php echo esc_attr( $opts['color_link_bg'] ); ?>;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: linear-gradient(135deg, var(--bg-from) 0%, var(--bg-to) 100%);
            font-family: 'Inter','Raleway',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 20% 40%, rgba(99,102,241,.03) 0%, rgba(0,0,0,0) 70%);
            pointer-events: none;
            z-index: 0;
        }
        .maintenance-card {
            max-width: 620px;
            width: 100%;
            background: var(--card-bg);
            border-radius: 48px;
            box-shadow: 0 25px 45px -12px rgba(0,0,0,.2), 0 4px 12px rgba(0,0,0,.03), inset 0 1px 0 rgba(255,255,255,.8);
            padding: 2.5rem 2rem 3rem;
            text-align: center;
            transition: transform .25s ease, box-shadow .3s ease;
            border: 1px solid rgba(255,255,255,.6);
            position: relative;
            z-index: 2;
        }
        .maintenance-card:hover { transform: translateY(-4px); box-shadow: 0 32px 55px -14px rgba(0,0,0,.25); }
        .icon-wrapper {
            background: linear-gradient(145deg,#f0f3fe,#e9eef9);
            width: 110px; height: 110px;
            border-radius: 60px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.8rem;
            box-shadow: inset 0 1px 3px rgba(0,0,0,.02), 0 12px 20px -8px rgba(0,0,0,.1);
            border: 1px solid rgba(255,255,255,.7);
        }
        .icon-wrapper i { font-size: 3.8rem; color: var(--icon-color); filter: drop-shadow(0 2px 4px rgba(0,0,0,.05)); }
        .icon-wrapper:hover .fa-tools { transform: rotate(12deg); transition: transform .3s ease; }
        h1 {
            font-size: 2.4rem; font-weight: 800;
            background: linear-gradient(130deg, var(--title-from), var(--title-to));
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
            letter-spacing: -.02em;
        }
        .msg-text {
            font-size: 1.18rem; line-height: 1.5;
            color: var(--body-text); font-weight: 450;
            background: rgba(235,245,255,.6);
            padding: 1.2rem 1.5rem;
            border-radius: 60px;
            margin: 1.5rem 0 1rem;
            border: 1px solid rgba(255,255,255,.8);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.7), 0 2px 6px rgba(0,0,0,.02);
        }
        .msg-text i { margin-right: 8px; color: #3b6e9e; }
        .support-note {
            font-size: .95rem; color: #5a6e84;
            background: rgba(255,255,255,.8);
            display: inline-flex; align-items: center; gap: 8px;
            padding: .6rem 1.2rem;
            border-radius: 100px;
            margin-top: 1.5rem;
            border: 1px solid #e2edf7;
        }
        .progress-section { margin: 2rem 0 1.2rem; }
        .eta-message {
            display: flex; align-items: center; justify-content: center;
            gap: 12px; flex-wrap: wrap;
            font-size: .9rem; font-weight: 500; color: #2c6280;
            background: #eef3fc;
            padding: .65rem 1rem;
            border-radius: 100px;
            width: fit-content;
            margin: 0 auto;
        }
        .progress-track {
            background: #e2e8f0;
            border-radius: 40px;
            height: 8px;
            width: 100%;
            margin: 1rem 0 .4rem;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.05);
        }
        .progress-fill {
            width: <?php echo $progress; ?>%;
            height: 100%;
            background: linear-gradient(90deg, var(--prog-from), var(--prog-to));
            border-radius: 40px;
            animation: pulseProgress 1.8s infinite ease;
            box-shadow: 0 0 2px var(--prog-to);
        }
        @keyframes pulseProgress {
            0%   { opacity:.7; width: <?php echo $kf_start; ?>; }
            50%  { opacity:1;  width: <?php echo $kf_mid; ?>; }
            100% { opacity:.7; width: <?php echo $kf_end; ?>; }
        }
        .status-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fef9e3;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: .75rem; font-weight: 600; color: #b2601a;
            margin-top: 12px;
            border: 1px solid #ffe6b3;
        }
        .contact-row { display: flex; justify-content: center; gap: 28px; margin-top: 2rem; flex-wrap: wrap; }
        .contact-link {
            text-decoration: none;
            font-size: .85rem; font-weight: 500;
            color: var(--link-text);
            background: var(--link-bg);
            padding: 8px 18px;
            border-radius: 40px;
            transition: all .2s ease;
            display: inline-flex; align-items: center; gap: 8px;
            border: 1px solid #dce5f0;
        }
        .contact-link:hover { background: #e6edf6; color: #1f4a73; transform: scale(.97); border-color: #bfd2e6; }
        .footer-note {
            font-size: .7rem; color: #8ba0b5;
            margin-top: 2.2rem;
            border-top: 1px dashed #dce5f0;
            padding-top: 1.5rem;
            letter-spacing: .3px;
        }
        @media (max-width:550px) {
            .maintenance-card { padding: 1.8rem 1.5rem 2rem; }
            h1 { font-size: 1.9rem; }
            .msg-text { font-size: 1rem; padding: 1rem; }
            .icon-wrapper { width: 85px; height: 85px; }
            .icon-wrapper i { font-size: 2.8rem; }
            .contact-link { padding: 6px 14px; font-size: .8rem; }
        }
    </style>
</head>
<body>
<section class="maintenance-card">
    <div class="icon-wrapper">
        <i class="fas fa-tools"></i>
    </div>

    <h1><?php echo esc_html( $opts['site_name'] ); ?></h1>

    <div class="msg-text">
        <i class="fas fa-hard-hat"></i>
        <?php echo esc_html( $opts['tagline'] ); ?>
    </div>

    <?php if ( $opts['show_progress'] === '1' ) : ?>
    <div class="progress-section">
        <div class="eta-message">
            <i class="fas fa-hourglass-half"></i>
            <span><?php echo wp_kses_post( $opts['eta_text'] ); ?></span>
        </div>
        <div class="progress-track">
            <div class="progress-fill"></div>
        </div>
        <div class="status-badge">
            <i class="fas fa-sync-alt fa-fw"></i>
            <span><?php echo esc_html( $opts['status_badge_text'] ); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <div class="contact-row">
        <?php if ( $opts['show_email_link'] === '1' && ! empty( $opts['support_email'] ) ) : ?>
        <a href="mailto:<?php echo esc_attr( $opts['support_email'] ); ?>?subject=Maintenance%20Inquiry" class="contact-link">
            <i class="fas fa-envelope"></i> Support
        </a>
        <?php endif; ?>

        <?php if ( $opts['show_status_btn'] === '1' ) : ?>
        <a href="#" class="contact-link" id="kwl-refresh">
            <i class="fas fa-redo-alt"></i> Check status
        </a>
        <?php endif; ?>

        <?php if ( $opts['show_fb_link'] === '1' && ! empty( $opts['facebook_url'] ) ) : ?>
        <a href="<?php echo esc_url( $opts['facebook_url'] ); ?>" class="contact-link" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-facebook-f"></i> Updates
        </a>
        <?php endif; ?>
    </div>

    <?php if ( ! empty( $opts['support_note'] ) ) : ?>
    <div class="support-note">
        <i class="fas fa-clock"></i>
        <span><?php echo esc_html( $opts['support_note'] ); ?></span>
    </div>
    <?php endif; ?>

    <div class="footer-note">
        <i class="far fa-smile"></i>
        <?php echo esc_html( $opts['footer_note'] ); ?> &bull; <span id="kwl-year"></span> <?php echo esc_html( $opts['site_name'] ); ?>
    </div>
</section>

<script>
(function(){
    document.getElementById('kwl-year').textContent = new Date().getFullYear();

    var btn = document.getElementById('kwl-refresh');
    if(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            var now   = new Date();
            var timeStr = now.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
            var toast = document.querySelector('.kwl-toast');
            if(!toast){
                toast = document.createElement('div');
                toast.className = 'kwl-toast';
                Object.assign(toast.style, {
                    position:'fixed', bottom:'20px', left:'50%',
                    transform:'translateX(-50%)',
                    background:'#1e2a3a', color:'white',
                    padding:'8px 20px', borderRadius:'40px',
                    fontSize:'.8rem', fontWeight:'500',
                    boxShadow:'0 4px 12px rgba(0,0,0,.2)',
                    zIndex:'999', transition:'opacity .4s',
                    fontFamily:"'Inter',sans-serif",
                    pointerEvents:'none'
                });
                document.body.appendChild(toast);
            }
            toast.innerHTML = '<i class="fas fa-check-circle" style="margin-right:6px;"></i>Last check: ' + timeStr + ' — site construction in progress.';
            toast.style.opacity = '1';
            setTimeout(function(){
                toast.style.opacity = '0';
                setTimeout(function(){ if(toast.parentNode) toast.remove(); }, 500);
            }, 2800);
        });
    }
})();
</script>
</body>
</html>
    <?php
}
