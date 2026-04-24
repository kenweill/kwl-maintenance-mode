<?php
/**
 * Plugin Name: KWL Maintenance Mode
 * Plugin URI:  https://github.com/kenweill/kwl-maintenance-mode
 * Description: A fully customizable maintenance/under-construction page with two built-in templates.
 * Version:     2.1.5
 * Author:      Ken Weill
 * Author URI:  https://github.com/kenweill
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'KWL_MAINT_VERSION', '2.1.5' );
define( 'KWL_MAINT_OPTIONS', 'kwl_maintenance_options' );

/* ---------------------------------------------------------------
   DEFAULTS
--------------------------------------------------------------- */
function kwl_maint_defaults() {
    return array(
        'mode'                        => 'off',
        'template'                    => 'business',
        'site_name'                   => get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'KWL Hub',
        'icon'                        => 'fa-tools',
        'tagline'                     => "We're building something great! Check back soon.",
        'eta_text'                    => 'Estimated completion: <strong>in progress</strong> &nbsp;&bull;&nbsp; Construction in progress',
        'status_badge_text'           => 'Our team is setting things up',
        'support_note'                => 'Site under construction. Exciting updates coming soon!',
        'footer_note'                 => 'Thanks for your patience',
        'show_progress'               => '1',
        'progress_value'              => '68',
        'support_email'               => 'support@kwlhub.com',
        'facebook_url'                => 'https://www.facebook.com/KWLHub',
        'show_email_link'             => '1',
        'show_fb_link'                => '1',
        'show_status_btn'             => '1',
        'color_bg_from'               => '#f5f7fc',
        'color_bg_to'                 => '#eef2f8',
        'color_card_bg'               => '#ffffff',
        'color_title'                 => '#1a2a3f',
        'color_title_to'              => '#2c4c7c',
        'color_body_text'             => '#2c3e50',
        'color_progress_from'         => '#2c7cb6',
        'color_progress_to'           => '#4f9fda',
        'color_icon'                  => '#2c3e66',
        'color_link'                  => '#3f6b9e',
        'color_link_bg'               => '#f0f4fa',
        'portfolio_name_badge'        => 'Ken Weill',
        'portfolio_subhead'           => '- thoughtfully curated -',
        'portfolio_tagline'           => "This site is currently under maintenance. I'm refreshing things behind the scenes - please check back soon!",
        'portfolio_status_badge'      => 'Fresh updates in progress',
        'portfolio_eta_text'          => 'Making things better &bull; Back shortly',
        'portfolio_footer_note'       => 'Thanks for your patience',
        'show_custom_link'            => '1',
        'custom_link_url'             => '/resume',
        'custom_link_label'           => 'View my resume',
        'custom_link_intro'           => 'Looking for my professional background?',
        'portfolio_color_bg_from'     => '#f8f9fc',
        'portfolio_color_bg_to'       => '#f0f2f8',
        'portfolio_color_title'       => '#1e2b3c',
        'portfolio_color_title_to'    => '#2c4c6e',
        'portfolio_color_prog_from'   => '#2c7cb6',
        'portfolio_color_prog_to'     => '#4f9fda',
        'portfolio_color_icon'        => '#2d3e5f',
        'portfolio_color_btn_bg'      => '#1e2f40',
        'portfolio_color_btn_text'    => '#ffffff',
        'bypass_admins'               => '1',
        'bypass_editors'              => '0',
        'meta_robots'                 => 'noindex, nofollow',
    );
}

function kwl_maint_options() {
    return wp_parse_args( get_option( KWL_MAINT_OPTIONS, array() ), kwl_maint_defaults() );
}

register_activation_hook( __FILE__, 'kwl_maint_activate' );
function kwl_maint_activate() {
    if ( false === get_option( KWL_MAINT_OPTIONS ) ) {
        add_option( KWL_MAINT_OPTIONS, kwl_maint_defaults() );
    }
}

/* ---------------------------------------------------------------
   ADMIN MENU
--------------------------------------------------------------- */
add_action( 'admin_menu', 'kwl_maint_add_menu' );
function kwl_maint_add_menu() {
    add_options_page( 'KWL Maintenance', 'KWL Maintenance', 'manage_options', 'kwl-maintenance', 'kwl_maint_settings_page' );
}

add_action( 'admin_init', 'kwl_maint_register_settings' );
function kwl_maint_register_settings() {
    register_setting( 'kwl_maintenance_group', KWL_MAINT_OPTIONS, array( 'sanitize_callback' => 'kwl_maint_sanitize' ) );
}

/* ---------------------------------------------------------------
   SANITIZE
--------------------------------------------------------------- */
function kwl_maint_sanitize( $input ) {
    $defaults = kwl_maint_defaults();
    $clean    = array();

    $text_fields = array(
        'site_name', 'icon', 'tagline', 'eta_text', 'status_badge_text', 'support_note',
        'footer_note', 'support_email', 'facebook_url', 'meta_robots',
        'portfolio_name_badge', 'portfolio_subhead', 'portfolio_tagline',
        'portfolio_status_badge', 'portfolio_eta_text', 'portfolio_footer_note',
        'custom_link_url', 'custom_link_label', 'custom_link_intro',
    );
    $hex_fields = array(
        'color_bg_from', 'color_bg_to', 'color_card_bg', 'color_title', 'color_title_to',
        'color_body_text', 'color_progress_from', 'color_progress_to', 'color_icon',
        'color_link', 'color_link_bg',
        'portfolio_color_bg_from', 'portfolio_color_bg_to', 'portfolio_color_title',
        'portfolio_color_title_to', 'portfolio_color_prog_from', 'portfolio_color_prog_to',
        'portfolio_color_icon', 'portfolio_color_btn_bg', 'portfolio_color_btn_text',
    );
    $bool_fields = array(
        'show_progress', 'show_email_link', 'show_fb_link', 'show_status_btn',
        'bypass_admins', 'bypass_editors', 'show_custom_link',
    );

    foreach ( $text_fields as $f ) {
        $clean[ $f ] = isset( $input[ $f ] ) ? wp_kses_post( $input[ $f ] ) : $defaults[ $f ];
    }
    foreach ( $hex_fields as $f ) {
        $v = isset( $input[ $f ] ) ? sanitize_hex_color( $input[ $f ] ) : '';
        $clean[ $f ] = $v ? $v : $defaults[ $f ];
    }
    foreach ( $bool_fields as $f ) {
        $clean[ $f ] = ( isset( $input[ $f ] ) && $input[ $f ] ) ? '1' : '0';
    }

    $clean['progress_value'] = isset( $input['progress_value'] ) ? max( 0, min( 100, intval( $input['progress_value'] ) ) ) : 68;
    $clean['template']       = ( isset( $input['template'] ) && in_array( $input['template'], array( 'business', 'portfolio' ), true ) ) ? $input['template'] : 'business';
    $clean['mode']           = ( isset( $input['mode'] ) && in_array( $input['mode'], array( 'off', 'maintenance', 'coming_soon' ), true ) ) ? $input['mode'] : 'off';

    // Auto-set robots meta based on mode — prevents mismatched settings.
    if ( $clean['mode'] === 'maintenance' ) {
        $clean['meta_robots'] = 'noindex, nofollow';
    } elseif ( $clean['mode'] === 'coming_soon' ) {
        $clean['meta_robots'] = 'index, follow';
    } else {
        $clean['meta_robots'] = 'noindex, nofollow'; // off — safe default, plugin not active anyway
    }

    return $clean;
}

/* ---------------------------------------------------------------
   ICON HELPER
--------------------------------------------------------------- */
function kwl_maint_icon( $icon ) {
    $icon = sanitize_html_class( $icon );
    return '<i class="fas ' . esc_attr( $icon ) . '" aria-hidden="true"></i>';
}

/* ---------------------------------------------------------------
   ADMIN SETTINGS PAGE
--------------------------------------------------------------- */
function kwl_maint_settings_page() {
    $opts = kwl_maint_options();
    $mode = $opts['mode'];
    $tpl  = $opts['template'];

    $pill_class = array( 'off' => 'inactive', 'coming_soon' => 'coming-soon', 'maintenance' => 'active' );
    $pill_label = array( 'off' => '&#9711; OFF', 'coming_soon' => '&#9679; COMING SOON', 'maintenance' => '&#9679; MAINTENANCE' );

    $icons = array(
        'fa-tools'          => '🔧 Tools',
        'fa-laptop-code'    => '💻 Laptop Code',
        'fa-hard-hat'       => '🪖 Hard Hat',
        'fa-paint-roller'   => '🖌 Paint Roller',
        'fa-wrench'         => '🔩 Wrench',
        'fa-cog'            => '⚙️ Cog',
        'fa-rocket'         => '🚀 Rocket',
        'fa-magic'          => '✨ Magic',
        'fa-user-astronaut' => '👨‍🚀 Astronaut',
        'fa-flask'          => '🧪 Flask',
    );
    ?>
    <div class="wrap kwl-wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            &#x1F527; KWL Maintenance Mode
            <span class="kwl-pill <?php echo esc_attr( $pill_class[ $mode ] ); ?>"><?php echo $pill_label[ $mode ]; ?></span>
            <span class="kwl-vpill">v<?php echo KWL_MAINT_VERSION; ?></span>
        </h1>
        <p style="color:#666;margin-bottom:20px;">Choose a mode, pick a template, then customize your page.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'kwl_maintenance_group' ); ?>

            <!-- MODE -->
            <div class="kwl-card">
                <h2>&#x1F6A6; Site Mode</h2>
                <div class="kwl-3col">
                    <label class="kwl-optcard <?php echo $mode === 'off' ? 'sel' : ''; ?>">
                        <input type="radio" name="<?php echo KWL_MAINT_OPTIONS; ?>[mode]" value="off" <?php checked( $mode, 'off' ); ?>>
                        <span class="kwl-oc-icon">&#x1F7E2;</span>
                        <span class="kwl-oc-label">Off</span>
                        <span class="kwl-oc-desc">Site is live for everyone.</span>
                    </label>
                    <label class="kwl-optcard <?php echo $mode === 'coming_soon' ? 'sel' : ''; ?>">
                        <input type="radio" name="<?php echo KWL_MAINT_OPTIONS; ?>[mode]" value="coming_soon" <?php checked( $mode, 'coming_soon' ); ?>>
                        <span class="kwl-oc-icon">&#x1F680;</span>
                        <span class="kwl-oc-label">Coming Soon</span>
                        <span class="kwl-oc-desc">Returns <code>200 OK</code>. Search engines can index this page.</span>
                    </label>
                    <label class="kwl-optcard <?php echo $mode === 'maintenance' ? 'sel' : ''; ?>">
                        <input type="radio" name="<?php echo KWL_MAINT_OPTIONS; ?>[mode]" value="maintenance" <?php checked( $mode, 'maintenance' ); ?>>
                        <span class="kwl-oc-icon">&#x1F527;</span>
                        <span class="kwl-oc-label">Maintenance</span>
                        <span class="kwl-oc-desc">Returns <code>503</code> + Retry-After. Tells search engines it is temporary.</span>
                    </label>
                </div>
            </div>

            <!-- TEMPLATE -->
            <div class="kwl-card">
                <h2>&#x1F3A8; Template</h2>
                <div class="kwl-2col">
                    <label class="kwl-optcard <?php echo $tpl === 'business' ? 'sel' : ''; ?>">
                        <input type="radio" name="<?php echo KWL_MAINT_OPTIONS; ?>[template]" value="business" <?php checked( $tpl, 'business' ); ?>>
                        <div class="kwl-tpl-preview" style="background:linear-gradient(135deg,#f5f7fc,#eef2f8);">
                            <div style="font-size:1.4rem;">&#x1F527;</div>
                            <div style="font-weight:700;font-size:.9rem;color:#1a2a3f;"><?php echo esc_html( $opts['site_name'] ); ?></div>
                            <div style="width:80%;height:4px;background:#e2e8f0;border-radius:10px;overflow:hidden;"><div style="width:68%;height:100%;background:#2c7cb6;border-radius:10px;"></div></div>
                        </div>
                        <span class="kwl-oc-label">Business / Brand</span>
                        <span class="kwl-oc-desc">Best for company or product sites.</span>
                    </label>
                    <label class="kwl-optcard <?php echo $tpl === 'portfolio' ? 'sel' : ''; ?>">
                        <input type="radio" name="<?php echo KWL_MAINT_OPTIONS; ?>[template]" value="portfolio" <?php checked( $tpl, 'portfolio' ); ?>>
                        <div class="kwl-tpl-preview" style="background:linear-gradient(135deg,#f8f9fc,#f0f2f8);">
                            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;color:#6c7a8e;background:#eef2f8;padding:2px 10px;border-radius:20px;"><?php echo esc_html( $opts['portfolio_name_badge'] ); ?></div>
                            <div style="font-weight:700;font-size:.9rem;color:#1e2b3c;"><?php echo esc_html( $opts['site_name'] ); ?></div>
                            <div style="font-size:.65rem;color:#6b7a8c;"><?php echo esc_html( $opts['portfolio_subhead'] ); ?></div>
                            <div style="font-size:.65rem;background:#1e2f40;color:#fff;padding:3px 10px;border-radius:20px;"><?php echo esc_html( $opts['custom_link_label'] ); ?></div>
                        </div>
                        <span class="kwl-oc-label">Personal / Portfolio</span>
                        <span class="kwl-oc-desc">Best for personal sites with a resume link.</span>
                    </label>
                </div>
            </div>

            <!-- TABS -->
            <div class="kwl-tabs">
                <button type="button" class="kwl-tab kwl-tab-active" data-tab="general">General</button>
                <button type="button" class="kwl-tab" data-tab="biz">Business Content</button>
                <button type="button" class="kwl-tab" data-tab="port">Portfolio Content</button>
                <button type="button" class="kwl-tab" data-tab="colors">Colors</button>
                <button type="button" class="kwl-tab" data-tab="links">Contact Links</button>
                <button type="button" class="kwl-tab" data-tab="access">Access &amp; SEO</button>
            </div>

            <!-- GENERAL TAB -->
            <div class="kwl-panel kwl-panel-active" id="kwl-tab-general">
                <div class="kwl-card">
                    <h2>&#x1F3E2; Identity</h2>
                    <table class="form-table">
                        <tr><th>Site / Company Name</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[site_name]" value="<?php echo esc_attr( $opts['site_name'] ); ?>" class="regular-text"></td></tr>
                        <tr><th>Page Icon</th><td>
                            <select name="<?php echo KWL_MAINT_OPTIONS; ?>[icon]">
                                <?php foreach ( $icons as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $opts['icon'], $val ); ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span style="color:#888;font-size:11px;"> Shown in the circle at the top</span>
                        </td></tr>
                    </table>
                </div>
                <div class="kwl-card">
                    <h2>&#x1F4CA; Progress Bar</h2>
                    <table class="form-table">
                        <tr><th>Show Progress Bar</th><td>
                            <label class="kwl-toggle"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_progress]" value="1" <?php checked( $opts['show_progress'], '1' ); ?>><span class="kwl-toggle-slider"></span></label>
                        </td></tr>
                        <tr><th>Progress % (0-100)</th><td>
                            <input type="range" min="0" max="100" name="<?php echo KWL_MAINT_OPTIONS; ?>[progress_value]" value="<?php echo esc_attr( $opts['progress_value'] ); ?>" style="width:220px;vertical-align:middle;" oninput="document.getElementById('kwl-pval').textContent=this.value+'%'">
                            <span id="kwl-pval" style="font-weight:600;color:#2271b1;margin-left:8px;"><?php echo esc_html( $opts['progress_value'] ); ?>%</span>
                        </td></tr>
                    </table>
                </div>
            </div>

            <!-- BUSINESS CONTENT TAB -->
            <div class="kwl-panel" id="kwl-tab-biz">
                <div class="kwl-card">
                    <h2>&#x270F;&#xFE0F; Business Template - Text</h2>
                    <table class="form-table">
                        <tr><th>Main Message</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[tagline]" value="<?php echo esc_attr( $opts['tagline'] ); ?>" class="large-text"></td></tr>
                        <tr><th>ETA / Status Text <span style="color:#888;font-size:11px;">(HTML ok)</span></th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[eta_text]" value="<?php echo esc_attr( $opts['eta_text'] ); ?>" class="large-text"></td></tr>
                        <tr><th>Status Badge Text</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[status_badge_text]" value="<?php echo esc_attr( $opts['status_badge_text'] ); ?>" class="large-text"></td></tr>
                        <tr><th>Support Note</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[support_note]" value="<?php echo esc_attr( $opts['support_note'] ); ?>" class="large-text"></td></tr>
                        <tr><th>Footer Text</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[footer_note]" value="<?php echo esc_attr( $opts['footer_note'] ); ?>" class="large-text"></td></tr>
                    </table>
                </div>
            </div>

            <!-- PORTFOLIO CONTENT TAB -->
            <div class="kwl-panel" id="kwl-tab-port">
                <div class="kwl-card">
                    <h2>&#x270F;&#xFE0F; Portfolio Template - Text</h2>
                    <table class="form-table">
                        <tr><th>Name Badge</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_name_badge]" value="<?php echo esc_attr( $opts['portfolio_name_badge'] ); ?>" class="regular-text"></td></tr>
                        <tr><th>Subheading</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_subhead]" value="<?php echo esc_attr( $opts['portfolio_subhead'] ); ?>" class="regular-text"></td></tr>
                        <tr><th>Main Message</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_tagline]" value="<?php echo esc_attr( $opts['portfolio_tagline'] ); ?>" class="large-text"></td></tr>
                        <tr><th>ETA / Status Text <span style="color:#888;font-size:11px;">(HTML ok)</span></th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_eta_text]" value="<?php echo esc_attr( $opts['portfolio_eta_text'] ); ?>" class="large-text"></td></tr>
                        <tr><th>Status Badge Text</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_status_badge]" value="<?php echo esc_attr( $opts['portfolio_status_badge'] ); ?>" class="large-text"></td></tr>
                        <tr><th>Footer Text</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_footer_note]" value="<?php echo esc_attr( $opts['portfolio_footer_note'] ); ?>" class="large-text"></td></tr>
                    </table>
                </div>
                <div class="kwl-card">
                    <h2>&#x1F517; Custom Link Block</h2>
                    <table class="form-table">
                        <tr><th>Show Link Block</th><td>
                            <label class="kwl-toggle"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_custom_link]" value="1" <?php checked( $opts['show_custom_link'], '1' ); ?>><span class="kwl-toggle-slider"></span></label>
                        </td></tr>
                        <tr><th>Intro Text</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[custom_link_intro]" value="<?php echo esc_attr( $opts['custom_link_intro'] ); ?>" class="large-text"></td></tr>
                        <tr><th>Button Label</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[custom_link_label]" value="<?php echo esc_attr( $opts['custom_link_label'] ); ?>" class="regular-text"></td></tr>
                        <tr><th>Button URL</th><td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[custom_link_url]" value="<?php echo esc_attr( $opts['custom_link_url'] ); ?>" class="regular-text" placeholder="/resume or https://..."></td></tr>
                    </table>
                </div>
            </div>

            <!-- COLORS TAB -->
            <div class="kwl-panel" id="kwl-tab-colors">
                <div class="kwl-card">
                    <h2>&#x1F3A8; Business Template - Colors</h2>
                    <table class="form-table">
                        <?php
                        $biz_colors = array(
                            'color_bg_from'       => 'Background Gradient Start',
                            'color_bg_to'         => 'Background Gradient End',
                            'color_card_bg'       => 'Card Background',
                            'color_title'         => 'Title Gradient Start',
                            'color_title_to'      => 'Title Gradient End',
                            'color_body_text'     => 'Body Text',
                            'color_progress_from' => 'Progress Bar Start',
                            'color_progress_to'   => 'Progress Bar End',
                            'color_icon'          => 'Icon Background Color',
                            'color_link'          => 'Button Text Color',
                            'color_link_bg'       => 'Button Background',
                        );
                        foreach ( $biz_colors as $k => $l ) :
                        ?>
                        <tr>
                            <th><?php echo esc_html( $l ); ?></th>
                            <td>
                                <input type="color" name="<?php echo KWL_MAINT_OPTIONS; ?>[<?php echo esc_attr( $k ); ?>]" value="<?php echo esc_attr( $opts[ $k ] ); ?>" class="kwl-color">
                                <input type="text" class="kwl-hex" value="<?php echo esc_attr( $opts[ $k ] ); ?>" readonly>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="kwl-card">
                    <h2>&#x1F3A8; Portfolio Template - Colors</h2>
                    <table class="form-table">
                        <?php
                        $port_colors = array(
                            'portfolio_color_bg_from'   => 'Background Gradient Start',
                            'portfolio_color_bg_to'     => 'Background Gradient End',
                            'portfolio_color_title'     => 'Title Gradient Start',
                            'portfolio_color_title_to'  => 'Title Gradient End',
                            'portfolio_color_prog_from' => 'Progress Bar Start',
                            'portfolio_color_prog_to'   => 'Progress Bar End',
                            'portfolio_color_icon'      => 'Icon Background Color',
                            'portfolio_color_btn_bg'    => 'Link Button Background',
                            'portfolio_color_btn_text'  => 'Link Button Text',
                        );
                        foreach ( $port_colors as $k => $l ) :
                        ?>
                        <tr>
                            <th><?php echo esc_html( $l ); ?></th>
                            <td>
                                <input type="color" name="<?php echo KWL_MAINT_OPTIONS; ?>[<?php echo esc_attr( $k ); ?>]" value="<?php echo esc_attr( $opts[ $k ] ); ?>" class="kwl-color">
                                <input type="text" class="kwl-hex" value="<?php echo esc_attr( $opts[ $k ] ); ?>" readonly>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- LINKS TAB -->
            <div class="kwl-panel" id="kwl-tab-links">
                <div class="kwl-card">
                    <h2>&#x1F517; Contact &amp; Social Links <span style="color:#888;font-size:12px;font-weight:400;">(Business template)</span></h2>
                    <table class="form-table">
                        <tr><th>Support Email</th><td>
                            <input type="email" name="<?php echo KWL_MAINT_OPTIONS; ?>[support_email]" value="<?php echo esc_attr( $opts['support_email'] ); ?>" class="regular-text">
                            <label style="margin-left:10px;"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_email_link]" value="1" <?php checked( $opts['show_email_link'], '1' ); ?>> Show</label>
                        </td></tr>
                        <tr><th>Facebook Page URL</th><td>
                            <input type="url" name="<?php echo KWL_MAINT_OPTIONS; ?>[facebook_url]" value="<?php echo esc_attr( $opts['facebook_url'] ); ?>" class="regular-text">
                            <label style="margin-left:10px;"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_fb_link]" value="1" <?php checked( $opts['show_fb_link'], '1' ); ?>> Show</label>
                        </td></tr>
                        <tr><th>"Check Status" Button</th><td>
                            <label><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_status_btn]" value="1" <?php checked( $opts['show_status_btn'], '1' ); ?>> Show</label>
                        </td></tr>
                    </table>
                </div>
            </div>

            <!-- ACCESS TAB -->
            <div class="kwl-panel" id="kwl-tab-access">
                <div class="kwl-card">
                    <h2>&#x1F510; Bypass Roles</h2>
                    <table class="form-table">
                        <tr><th>Administrators</th><td><label><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[bypass_admins]" value="1" <?php checked( $opts['bypass_admins'], '1' ); ?>> Always see the live site</label></td></tr>
                        <tr><th>Editors</th><td><label><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[bypass_editors]" value="1" <?php checked( $opts['bypass_editors'], '1' ); ?>> Can bypass</label></td></tr>
                    </table>
                </div>
                <div class="kwl-card">
                    <h2>&#x1F916; SEO &amp; Robots Meta</h2>
                    <p style="color:#555;font-size:13px;margin-bottom:16px;">The robots meta tag is set <strong>automatically</strong> based on the mode you select. No manual adjustment needed.</p>
                    <table class="form-table">
                        <tr>
                            <th>&#x1F680; Coming Soon</th>
                            <td><code>200 OK</code> &nbsp;+&nbsp; <code>index, follow</code> &mdash; search engines can index your page.</td>
                        </tr>
                        <tr>
                            <th>&#x1F527; Maintenance</th>
                            <td><code>503</code> + <code>Retry-After</code> &nbsp;+&nbsp; <code>noindex, nofollow</code> &mdash; search engines know it's temporary.</td>
                        </tr>
                        <tr>
                            <th>Current robots tag</th>
                            <td><code><?php echo esc_html( $opts['meta_robots'] ); ?></code> &nbsp;<span style="color:#888;font-size:12px;">(set automatically when you save)</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
                <?php submit_button( 'Save Settings', 'primary large', 'submit', false ); ?>
                <a href="<?php echo esc_url( add_query_arg( 'kwl_preview', '1', home_url( '/' ) ) ); ?>" target="_blank" class="button button-secondary button-large">&#x1F441; Preview Page</a>
            </div>
        </form>
    </div>

    <style>
    .kwl-wrap { max-width:940px; }
    .kwl-pill { font-size:12px;padding:4px 12px;border-radius:20px;font-weight:600; }
    .kwl-pill.active      { background:#d4edda;color:#155724; }
    .kwl-pill.coming-soon { background:#d1ecf1;color:#0c5460; }
    .kwl-pill.inactive    { background:#e2e3e5;color:#383d41; }
    .kwl-vpill { font-size:11px;padding:3px 10px;border-radius:20px;background:#f0f0f0;color:#666;font-weight:500; }
    .kwl-card { background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:20px 24px;margin-bottom:16px; }
    .kwl-card h2 { font-size:15px;margin:0 0 16px;padding:0;border:none; }
    .kwl-3col,.kwl-2col { display:flex;gap:12px;flex-wrap:wrap;margin-top:8px; }
    .kwl-optcard { flex:1;min-width:160px;border:2px solid #e0e0e0;border-radius:10px;padding:14px;cursor:pointer;transition:all .2s;display:flex;flex-direction:column;gap:5px; }
    .kwl-optcard input[type=radio] { display:none; }
    .kwl-optcard:hover { border-color:#a0b4c8; }
    .kwl-optcard.sel { border-color:#2271b1;background:#f0f6fc; }
    .kwl-oc-icon { font-size:1.4rem; }
    .kwl-oc-label { font-weight:700;font-size:14px;color:#1d2327; }
    .kwl-oc-desc { font-size:12px;color:#666;line-height:1.4; }
    .kwl-oc-desc code { background:#eee;padding:1px 5px;border-radius:3px;font-size:11px; }
    .kwl-tpl-preview { border-radius:10px;padding:12px;text-align:center;min-height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;margin-bottom:4px; }
    .kwl-tabs { display:flex;gap:4px;border-bottom:2px solid #e0e0e0;margin:20px 0;flex-wrap:wrap; }
    .kwl-tab { background:none;border:none;padding:10px 16px;cursor:pointer;font-size:13px;font-weight:500;color:#555;border-bottom:2px solid transparent;margin-bottom:-2px;border-radius:4px 4px 0 0;transition:all .15s; }
    .kwl-tab:hover { background:#f5f5f5;color:#1d2327; }
    .kwl-tab-active { background:#fff!important;color:#2271b1!important;border-bottom-color:#2271b1!important;border:1px solid #e0e0e0;border-bottom:2px solid #fff; }
    .kwl-panel { display:none; }
    .kwl-panel-active { display:block; }
    .kwl-toggle { position:relative;display:inline-block;width:44px;height:24px; }
    .kwl-toggle input { opacity:0;width:0;height:0; }
    .kwl-toggle-slider { position:absolute;top:0;left:0;right:0;bottom:0;background:#ccc;border-radius:24px;transition:.3s;cursor:pointer; }
    .kwl-toggle-slider:before { content:'';position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:white;border-radius:50%;transition:.3s; }
    .kwl-toggle input:checked + .kwl-toggle-slider { background:#2271b1; }
    .kwl-toggle input:checked + .kwl-toggle-slider:before { transform:translateX(20px); }
    .kwl-color { width:44px;height:32px;border:1px solid #ddd;border-radius:4px;padding:2px;cursor:pointer;vertical-align:middle; }
    .kwl-hex { width:80px;margin-left:8px;font-family:monospace;font-size:12px;vertical-align:middle; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.kwl-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.kwl-tab').forEach(function(t) { t.classList.remove('kwl-tab-active'); });
                document.querySelectorAll('.kwl-panel').forEach(function(p) { p.classList.remove('kwl-panel-active'); });
                tab.classList.add('kwl-tab-active');
                document.getElementById('kwl-tab-' + tab.dataset.tab).classList.add('kwl-panel-active');
            });
        });
        document.querySelectorAll('.kwl-optcard').forEach(function(card) {
            card.addEventListener('click', function() {
                var group = card.closest('.kwl-3col, .kwl-2col');
                if (group) group.querySelectorAll('.kwl-optcard').forEach(function(c) { c.classList.remove('sel'); });
                card.classList.add('sel');
                card.querySelector('input[type=radio]').checked = true;
            });
        });
        document.querySelectorAll('.kwl-color').forEach(function(picker) {
            picker.addEventListener('input', function() {
                picker.nextElementSibling.value = picker.value;
            });
        });
    });
    </script>
    <?php
}

/* ---------------------------------------------------------------
   FRONT-END INTERCEPT
--------------------------------------------------------------- */
add_action( 'template_redirect', 'kwl_maint_intercept' );
function kwl_maint_intercept() {
    $opts       = kwl_maint_options();
    $mode       = $opts['mode'];
    $is_preview = isset( $_GET['kwl_preview'] ) && current_user_can( 'manage_options' );

    if ( $mode === 'off' && ! $is_preview ) return;

    if ( ! $is_preview && is_user_logged_in() ) {
        if ( $opts['bypass_admins'] === '1' && current_user_can( 'manage_options' ) ) return;
        if ( $opts['bypass_editors'] === '1' && current_user_can( 'edit_others_posts' ) ) return;
    }

    if ( is_admin() ) return;
    if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] === 'wp-login.php' ) return;

    $rp = isset( $_SERVER['REQUEST_URI'] ) ? strtolower( parse_url( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) ) : '';
    if ( substr( $rp, -12 ) === 'wp-login.php' ) return;

    if ( $opts['template'] === 'portfolio' ) {
        kwl_maint_render_portfolio( $opts, $mode );
    } else {
        kwl_maint_render_business( $opts, $mode );
    }
    exit;
}

/* ---------------------------------------------------------------
   SHARED PAGE HEAD
--------------------------------------------------------------- */
function kwl_maint_head( $title, $robots, $css, $mode ) {
    if ( $mode === 'maintenance' ) {
        status_header( 503 );
        header( 'Retry-After: 3600' );
    } else {
        status_header( 200 );
    }
    header( 'Content-Type: text/html; charset=UTF-8' );
    echo '<!DOCTYPE html><html lang="en"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">';
    echo '<meta name="robots" content="' . esc_attr( $robots ) . '">';
    echo '<title>' . esc_html( $title ) . '</title>';
    echo '<link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
    echo '<style>' . $css . '</style>';
    echo '</head><body>';
}

/* ---------------------------------------------------------------
   SHARED CHECK STATUS JS
--------------------------------------------------------------- */
function kwl_maint_status_js( $name ) {
    $msg = $name ? esc_js( $name ) . ' is working on it. Please revisit soon!' : 'Site construction is in progress.';
    echo '<script>
(function(){
    var y = document.getElementById("kwl-year");
    if(y) y.textContent = new Date().getFullYear();
    var btn = document.getElementById("kwl-refresh");
    if(!btn) return;
    btn.addEventListener("click", function(e){
        e.preventDefault();
        var t = new Date().toLocaleTimeString([],{hour:"2-digit",minute:"2-digit"});
        var toast = document.querySelector(".kwl-toast");
        if(!toast){
            toast = document.createElement("div");
            toast.className = "kwl-toast";
            Object.assign(toast.style,{position:"fixed",bottom:"20px",left:"50%",transform:"translateX(-50%)",background:"#1e2a3a",color:"white",padding:"8px 20px",borderRadius:"40px",fontSize:".8rem",fontWeight:"500",boxShadow:"0 4px 12px rgba(0,0,0,.2)",zIndex:"999",transition:"opacity .4s",pointerEvents:"none",fontFamily:"Inter,sans-serif"});
            document.body.appendChild(toast);
        }
        toast.textContent = "Last check: " + t + " - ' . $msg . '";
        toast.style.opacity = "1";
        setTimeout(function(){ toast.style.opacity="0"; setTimeout(function(){ if(toast.parentNode) toast.remove(); },500); },2800);
    });
})();
</script>';
}

/* ---------------------------------------------------------------
   TEMPLATE 1 - BUSINESS
--------------------------------------------------------------- */
function kwl_maint_render_business( $opts, $mode ) {
    $p    = intval( $opts['progress_value'] );
    $pmax = min( 100, $p + 6 );
    $pmin = max( 0,   $p - 4 );
    $name = $opts['site_name'];

    $title = $mode === 'coming_soon'
        ? esc_html( $name ) . ' - Coming Soon'
        : esc_html( $name ) . ' - Under Construction';

    $css = "
*{margin:0;padding:0;box-sizing:border-box;}
body{background:linear-gradient(135deg,{$opts['color_bg_from']} 0%,{$opts['color_bg_to']} 100%);font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
.card{max-width:620px;width:100%;background:{$opts['color_card_bg']};border-radius:48px;box-shadow:0 25px 45px -12px rgba(0,0,0,.2),inset 0 1px 0 rgba(255,255,255,.8);padding:2.5rem 2rem 3rem;text-align:center;border:1px solid rgba(255,255,255,.6);}
.icon-wrap{background:linear-gradient(145deg,#f0f3fe,#e9eef9);width:110px;height:110px;border-radius:60px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.8rem;box-shadow:0 12px 20px -8px rgba(0,0,0,.1);border:1px solid rgba(255,255,255,.7);transition:transform .25s ease;}.icon-wrap:hover{transform:rotate(8deg) scale(1.04);}.icon-wrap i{font-size:3.6rem;color:{$opts['color_icon']};filter:drop-shadow(0 2px 4px rgba(0,0,0,.05));}
h1{font-size:2.4rem;font-weight:800;background:linear-gradient(130deg,{$opts['color_title']},{$opts['color_title_to']});background-clip:text;-webkit-background-clip:text;color:transparent;margin-bottom:1rem;letter-spacing:-.02em;}
.msg{font-size:1.1rem;line-height:1.5;color:{$opts['color_body_text']};background:rgba(235,245,255,.6);padding:1.2rem 1.5rem;border-radius:60px;margin:1.5rem 0 1rem;border:1px solid rgba(255,255,255,.8);}
.eta-wrap{margin:2rem 0 1.2rem;}
.eta{display:inline-flex;align-items:center;gap:10px;font-size:.9rem;font-weight:500;color:#2c6280;background:#eef3fc;padding:.65rem 1rem;border-radius:100px;}
.prog-track{background:#e2e8f0;border-radius:40px;height:8px;margin:1rem 0 .4rem;overflow:hidden;}
.prog-fill{width:{$p}%;height:100%;background:linear-gradient(90deg,{$opts['color_progress_from']},{$opts['color_progress_to']});border-radius:40px;animation:pulse 1.8s infinite ease;}
@keyframes pulse{0%{opacity:.7;width:{$pmin}%;}50%{opacity:1;width:{$pmax}%;}100%{opacity:.7;width:{$pmin}%;}}
.badge{display:inline-flex;align-items:center;gap:6px;background:#fef9e3;padding:6px 14px;border-radius:50px;font-size:.75rem;font-weight:600;color:#b2601a;margin-top:12px;border:1px solid #ffe6b3;}
.links{display:flex;justify-content:center;gap:20px;margin-top:2rem;flex-wrap:wrap;}
.link{text-decoration:none;font-size:.85rem;font-weight:500;color:{$opts['color_link']};background:{$opts['color_link_bg']};padding:8px 18px;border-radius:40px;transition:all .2s;border:1px solid #dce5f0;}
.link:hover{background:#e6edf6;transform:scale(.97);}
.note{font-size:.95rem;color:#5a6e84;background:rgba(255,255,255,.8);display:inline-flex;align-items:center;gap:8px;padding:.6rem 1.2rem;border-radius:100px;margin-top:1.5rem;border:1px solid #e2edf7;}
.foot{font-size:.7rem;color:#8ba0b5;margin-top:2.2rem;border-top:1px dashed #dce5f0;padding-top:1.5rem;}
@media(max-width:550px){.card{padding:1.8rem 1.5rem 2rem;}h1{font-size:1.9rem;}.msg{font-size:1rem;padding:1rem;}.icon-wrap{width:85px;height:85px;}.link{padding:6px 14px;font-size:.8rem;}}
";

    kwl_maint_head( $title, $opts['meta_robots'], $css, $mode );
    ?>
    <div class="card">
        <div class="icon-wrap"><?php echo kwl_maint_icon( $opts['icon'] ); ?></div>
        <h1><?php echo esc_html( $name ); ?></h1>
        <div class="msg"><?php echo esc_html( $opts['tagline'] ); ?></div>

        <?php if ( $opts['show_progress'] === '1' ) : ?>
        <div class="eta-wrap">
            <div class="eta">&#x23F3; <span><?php echo wp_kses_post( $opts['eta_text'] ); ?></span></div>
            <div class="prog-track"><div class="prog-fill"></div></div>
            <div class="badge">&#x1F504; <?php echo esc_html( $opts['status_badge_text'] ); ?></div>
        </div>
        <?php endif; ?>

        <div class="links">
            <?php if ( $opts['show_email_link'] === '1' && ! empty( $opts['support_email'] ) ) : ?>
            <a href="mailto:<?php echo esc_attr( $opts['support_email'] ); ?>?subject=Maintenance+Inquiry" class="link">&#x2709; Support</a>
            <?php endif; ?>
            <?php if ( $opts['show_status_btn'] === '1' ) : ?>
            <a href="#" class="link" id="kwl-refresh">&#x21BA; Check status</a>
            <?php endif; ?>
            <?php if ( $opts['show_fb_link'] === '1' && ! empty( $opts['facebook_url'] ) ) : ?>
            <a href="<?php echo esc_url( $opts['facebook_url'] ); ?>" class="link" target="_blank" rel="noopener noreferrer">&#x1F44D; Updates</a>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $opts['support_note'] ) ) : ?>
        <div class="note">&#x23F0; <?php echo esc_html( $opts['support_note'] ); ?></div>
        <?php endif; ?>

        <div class="foot">&#x1F642; <?php echo esc_html( $opts['footer_note'] ); ?> &bull; <span id="kwl-year"></span> <?php echo esc_html( $name ); ?></div>
    </div>
    <?php
    kwl_maint_status_js( '' );
    echo '</body></html>';
}

/* ---------------------------------------------------------------
   TEMPLATE 2 - PORTFOLIO
--------------------------------------------------------------- */
function kwl_maint_render_portfolio( $opts, $mode ) {
    $p    = intval( $opts['progress_value'] );
    $pmax = min( 100, $p + 6 );
    $pmin = max( 0,   $p - 4 );
    $name = $opts['portfolio_name_badge'] ? $opts['portfolio_name_badge'] : $opts['site_name'];

    $title = $mode === 'coming_soon'
        ? esc_html( $name ) . ' - Coming Soon'
        : esc_html( $name ) . ' - Site Maintenance';

    $css = "
*{margin:0;padding:0;box-sizing:border-box;}
body{background:linear-gradient(135deg,{$opts['portfolio_color_bg_from']} 0%,{$opts['portfolio_color_bg_to']} 100%);font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
.card{max-width:600px;width:100%;background:rgba(255,255,255,.96);border-radius:48px;box-shadow:0 25px 45px -12px rgba(0,0,0,.15),inset 0 1px 0 rgba(255,255,255,.8);padding:2.8rem 2rem 3rem;text-align:center;border:1px solid rgba(255,255,255,.6);}
.icon-wrap{background:linear-gradient(145deg,#f0f2fe,#e8ecf9);width:110px;height:110px;border-radius:60px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.8rem;box-shadow:0 12px 20px -8px rgba(0,0,0,.08);border:1px solid rgba(255,255,255,.7);transition:transform .2s ease;}.icon-wrap:hover{transform:rotate(5deg) scale(1.04);}.icon-wrap i{font-size:3.6rem;color:{$opts['portfolio_color_icon']};filter:drop-shadow(0 2px 4px rgba(0,0,0,.05));}
.name-badge{font-size:.9rem;font-weight:600;letter-spacing:.3px;text-transform:uppercase;color:#6c7a8e;background:#eef2f8;display:inline-block;padding:.3rem 1rem;border-radius:40px;margin-bottom:1rem;}
h1{font-size:2.8rem;font-weight:700;background:linear-gradient(130deg,{$opts['portfolio_color_title']},{$opts['portfolio_color_title_to']});background-clip:text;-webkit-background-clip:text;color:transparent;margin-bottom:.75rem;letter-spacing:-.02em;}
.subhead{font-size:1rem;color:#6b7a8c;margin-bottom:.5rem;}
.msg{font-size:1.1rem;line-height:1.5;color:#2c3e50;background:rgba(235,245,255,.6);padding:1.2rem 1.5rem;border-radius:60px;margin:1.5rem 0 1rem;border:1px solid rgba(255,255,255,.8);}
.link-block{background:linear-gradient(135deg,#eef2ff,#e8edf9);border-radius:32px;padding:1.2rem 1.5rem;margin:1.5rem 0;border:1px solid rgba(79,70,229,.15);}
.link-block p{font-size:.95rem;color:#2c3e50;margin-bottom:.8rem;font-weight:500;}
.link-btn{display:inline-flex;align-items:center;gap:10px;background:{$opts['portfolio_color_btn_bg']};color:{$opts['portfolio_color_btn_text']};text-decoration:none;padding:.75rem 1.8rem;border-radius:60px;font-weight:600;font-size:.95rem;transition:all .2s;box-shadow:0 2px 6px rgba(0,0,0,.1);}
.link-btn:hover{opacity:.88;transform:scale(.98);}
.eta-wrap{margin:1rem 0 .5rem;}
.eta{display:inline-flex;align-items:center;gap:10px;font-size:.9rem;font-weight:500;color:#2c6280;background:#eef3fc;padding:.65rem 1rem;border-radius:100px;}
.prog-track{background:#e2e8f0;border-radius:40px;height:8px;margin:1rem 0 .4rem;overflow:hidden;}
.prog-fill{width:{$p}%;height:100%;background:linear-gradient(90deg,{$opts['portfolio_color_prog_from']},{$opts['portfolio_color_prog_to']});border-radius:40px;animation:pulse 1.8s infinite ease;}
@keyframes pulse{0%{opacity:.7;width:{$pmin}%;}50%{opacity:1;width:{$pmax}%;}100%{opacity:.7;width:{$pmin}%;}}
.badge{display:inline-flex;align-items:center;gap:6px;background:#fef9e3;padding:6px 14px;border-radius:50px;font-size:.75rem;font-weight:600;color:#b2601a;margin-top:12px;border:1px solid #ffe6b3;}
.action-row{display:flex;justify-content:center;gap:20px;margin-top:1.8rem;flex-wrap:wrap;}
.action-link{text-decoration:none;font-size:.85rem;font-weight:500;color:#4a6f8f;background:#f0f4fa;padding:8px 20px;border-radius:40px;transition:all .2s;border:1px solid #dce5f0;}
.action-link:hover{background:#e6edf6;transform:scale(.97);}
.foot{font-size:.7rem;color:#8ba0b5;margin-top:2rem;border-top:1px dashed #dce5f0;padding-top:1.5rem;}
@media(max-width:550px){.card{padding:1.8rem 1.5rem 2rem;}h1{font-size:2rem;}.icon-wrap{width:85px;height:85px;}.link-btn{padding:.6rem 1.4rem;font-size:.85rem;}}
";

    kwl_maint_head( $title, $opts['meta_robots'], $css, $mode );
    ?>
    <div class="card">
        <div class="icon-wrap"><?php echo kwl_maint_icon( $opts['icon'] ); ?></div>

        <?php if ( ! empty( $opts['portfolio_name_badge'] ) ) : ?>
        <div class="name-badge">&#x1F9D1; <?php echo esc_html( $opts['portfolio_name_badge'] ); ?></div>
        <?php endif; ?>

        <h1><?php echo esc_html( $opts['site_name'] ); ?></h1>

        <?php if ( ! empty( $opts['portfolio_subhead'] ) ) : ?>
        <div class="subhead"><?php echo esc_html( $opts['portfolio_subhead'] ); ?></div>
        <?php endif; ?>

        <div class="msg">&#x1F58C; <?php echo esc_html( $opts['portfolio_tagline'] ); ?></div>

        <?php if ( $opts['show_custom_link'] === '1' && ! empty( $opts['custom_link_url'] ) ) : ?>
        <div class="link-block">
            <?php if ( ! empty( $opts['custom_link_intro'] ) ) : ?>
            <p>&#x1F4C4; <?php echo esc_html( $opts['custom_link_intro'] ); ?></p>
            <?php endif; ?>
            <a href="<?php echo esc_url( $opts['custom_link_url'] ); ?>" class="link-btn" target="_blank" rel="noopener noreferrer">
                &#x1F517; <?php echo esc_html( $opts['custom_link_label'] ); ?>
            </a>
        </div>
        <?php endif; ?>

        <?php if ( $opts['show_progress'] === '1' ) : ?>
        <div class="eta-wrap">
            <div class="eta">&#x2728; <span><?php echo wp_kses_post( $opts['portfolio_eta_text'] ); ?></span></div>
            <div class="prog-track"><div class="prog-fill"></div></div>
            <div class="badge">&#x2615; <?php echo esc_html( $opts['portfolio_status_badge'] ); ?></div>
        </div>
        <?php endif; ?>

        <div class="action-row">
            <a href="#" class="action-link" id="kwl-refresh">&#x21BA; Check status</a>
        </div>

        <div class="foot">&#x23F0; <?php echo esc_html( $opts['portfolio_footer_note'] ); ?> &bull; <span id="kwl-year"></span> <?php echo esc_html( $name ); ?></div>
    </div>
    <?php
    kwl_maint_status_js( esc_js( $name ) );
    echo '</body></html>';
}
