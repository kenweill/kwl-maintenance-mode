<?php
/**
 * Plugin Name: KWL Maintenance Mode
 * Plugin URI:  https://github.com/kenweill/kwl-maintenance-mode
 * Description: A fully customizable maintenance/under-construction page with two built-in templates — a branded business style and a personal/portfolio style. Customize everything from the WordPress dashboard.
 * Version:     2.0.1
 * Author:      Ken Weill
 * Author URI:  https://github.com/kenweill
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'KWL_MAINT_VERSION', '2.0.1' );
define( 'KWL_MAINT_OPTIONS', 'kwl_maintenance_options' );

/* ---------------------------------------------------------------
   DEFAULTS
--------------------------------------------------------------- */
function kwl_maint_defaults() {
    return [
        // Toggle & template
        'enabled'             => '0',
        'template'            => 'business',   // 'business' | 'portfolio'

        // Shared — identity
        'site_name'           => get_bloginfo('name') ?: 'KWL Hub',
        'icon'                => 'fa-tools',   // Font Awesome class

        // Shared — content
        'tagline'             => "We're building something great! Check back soon.",
        'eta_text'            => 'Estimated completion: <strong>in progress</strong> &nbsp;•&nbsp; Construction in progress',
        'status_badge_text'   => 'Our team is setting things up',
        'support_note'        => 'Site under construction. Exciting updates coming soon!',
        'footer_note'         => 'Thanks for your patience',

        // Shared — progress bar
        'show_progress'       => '1',
        'progress_value'      => '68',

        // Shared — contact links
        'support_email'       => 'support@kwlhub.com',
        'facebook_url'        => 'https://www.facebook.com/KWLHub',
        'show_email_link'     => '1',
        'show_fb_link'        => '1',
        'show_status_btn'     => '1',

        // Shared — colors (business defaults)
        'color_bg_from'       => '#f5f7fc',
        'color_bg_to'         => '#eef2f8',
        'color_card_bg'       => '#ffffff',
        'color_title'         => '#1a2a3f',
        'color_title_to'      => '#2c4c7c',
        'color_body_text'     => '#2c3e50',
        'color_progress_from' => '#2c7cb6',
        'color_progress_to'   => '#4f9fda',
        'color_icon'          => '#2c3e66',
        'color_link'          => '#3f6b9e',
        'color_link_bg'       => '#f0f4fa',

        // Portfolio-specific
        'portfolio_name_badge'   => 'Ken Weill',
        'portfolio_subhead'      => '— thoughtfully curated —',
        'portfolio_tagline'      => "This site is currently under maintenance. I'm refreshing things behind the scenes — please check back soon!",
        'portfolio_status_badge' => 'Fresh updates in progress',
        'portfolio_eta_text'     => 'Making things better &nbsp;•&nbsp; Back shortly',
        'portfolio_footer_note'  => 'Thanks for your patience',

        // Portfolio — custom link (resume / project / etc.)
        'show_custom_link'       => '1',
        'custom_link_url'        => '/resume',
        'custom_link_label'      => 'View my resume →',
        'custom_link_intro'      => 'Looking for my professional background?',
        'custom_link_icon'       => 'fa-external-link-alt',

        // Portfolio — colors
        'portfolio_color_bg_from'       => '#f8f9fc',
        'portfolio_color_bg_to'         => '#f0f2f8',
        'portfolio_color_title'         => '#1e2b3c',
        'portfolio_color_title_to'      => '#2c4c6e',
        'portfolio_color_progress_from' => '#2c7cb6',
        'portfolio_color_progress_to'   => '#4f9fda',
        'portfolio_color_icon'          => '#2d3e5f',
        'portfolio_color_link_btn_bg'   => '#1e2f40',
        'portfolio_color_link_btn_text' => '#ffffff',

        // Access & SEO
        'bypass_admins'       => '1',
        'bypass_editors'      => '0',
        'meta_robots'         => 'noindex, nofollow',
    ];
}

function kwl_maint_options() {
    return wp_parse_args( get_option( KWL_MAINT_OPTIONS, [] ), kwl_maint_defaults() );
}

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

add_action( 'admin_init', function() {
    register_setting( 'kwl_maintenance_group', KWL_MAINT_OPTIONS, [
        'sanitize_callback' => 'kwl_maint_sanitize',
    ]);
});

/* ---------------------------------------------------------------
   SANITIZE
--------------------------------------------------------------- */
function kwl_maint_sanitize( $input ) {
    $defaults = kwl_maint_defaults();
    $clean    = [];

    $text_fields = [
        'site_name','icon','tagline','eta_text','status_badge_text','support_note',
        'footer_note','support_email','facebook_url','meta_robots',
        'portfolio_name_badge','portfolio_subhead','portfolio_tagline',
        'portfolio_status_badge','portfolio_eta_text','portfolio_footer_note',
        'custom_link_url','custom_link_label','custom_link_intro','custom_link_icon',
    ];
    $int_fields  = [ 'progress_value' ];
    $hex_fields  = [
        'color_bg_from','color_bg_to','color_card_bg','color_title','color_title_to',
        'color_body_text','color_progress_from','color_progress_to','color_icon',
        'color_link','color_link_bg',
        'portfolio_color_bg_from','portfolio_color_bg_to','portfolio_color_title',
        'portfolio_color_title_to','portfolio_color_progress_from',
        'portfolio_color_progress_to','portfolio_color_icon',
        'portfolio_color_link_btn_bg','portfolio_color_link_btn_text',
    ];
    $bool_fields = [
        'enabled','show_progress','show_email_link','show_fb_link','show_status_btn',
        'bypass_admins','bypass_editors','show_custom_link',
    ];
    $select_fields = [ 'template' ];

    foreach ( $text_fields as $f ) {
        $clean[$f] = isset($input[$f]) ? wp_kses_post($input[$f]) : $defaults[$f];
    }
    foreach ( $int_fields as $f ) {
        $v = isset($input[$f]) ? intval($input[$f]) : intval($defaults[$f]);
        $clean[$f] = max(0, min(100, $v));
    }
    foreach ( $hex_fields as $f ) {
        $v = isset($input[$f]) ? sanitize_hex_color($input[$f]) : $defaults[$f];
        $clean[$f] = $v ?: $defaults[$f];
    }
    foreach ( $bool_fields as $f ) {
        $clean[$f] = isset($input[$f]) && $input[$f] ? '1' : '0';
    }
    $clean['template'] = isset($input['template']) && in_array($input['template'], ['business','portfolio']) ? $input['template'] : 'business';

    return $clean;
}

/* ---------------------------------------------------------------
   ADMIN PAGE
--------------------------------------------------------------- */
function kwl_maint_settings_page() {
    $opts   = kwl_maint_options();
    $active = $opts['enabled'] === '1';
    $tpl    = $opts['template'];

    $icons = [
        'fa-tools'         => '🔧 Tools',
        'fa-laptop-code'   => '💻 Laptop Code',
        'fa-hard-hat'      => '🪖 Hard Hat',
        'fa-paint-roller'  => '🖌 Paint Roller',
        'fa-wrench'        => '🔩 Wrench',
        'fa-cog'           => '⚙️ Cog',
        'fa-rocket'        => '🚀 Rocket',
        'fa-magic'         => '✨ Magic',
        'fa-user-astronaut'=> '👨‍🚀 Astronaut',
        'fa-flask'         => '🧪 Flask',
    ];
    ?>
    <div class="wrap kwl-admin-wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            🔧 KWL Maintenance Mode
            <span class="kwl-status-pill <?php echo $active ? 'active' : 'inactive'; ?>">
                <?php echo $active ? '● ACTIVE' : '○ INACTIVE'; ?>
            </span>
            <span class="kwl-version-pill">v<?php echo KWL_MAINT_VERSION; ?></span>
        </h1>
        <p class="kwl-desc">Customize your maintenance page and enable it when ready. Two templates available.</p>

        <form method="post" action="options.php">
            <?php settings_fields('kwl_maintenance_group'); ?>

            <!-- ===== TEMPLATE SWITCHER ===== -->
            <div class="kwl-card kwl-template-switcher">
                <h2>🎨 Choose Template</h2>
                <div class="kwl-template-row">
                    <label class="kwl-template-card <?php echo $tpl === 'business' ? 'selected' : ''; ?>">
                        <input type="radio" name="<?php echo KWL_MAINT_OPTIONS; ?>[template]" value="business" <?php checked($tpl,'business'); ?>>
                        <div class="kwl-tpl-preview business-preview">
                            <div class="tpl-icon">🔧</div>
                            <div class="tpl-name">KWL Hub</div>
                            <div class="tpl-bar"><div class="tpl-fill"></div></div>
                        </div>
                        <span class="kwl-tpl-label">Business / Brand</span>
                        <span class="kwl-tpl-desc">Best for company or product sites. Shows logo, tagline, progress bar, and contact links.</span>
                    </label>

                    <label class="kwl-template-card <?php echo $tpl === 'portfolio' ? 'selected' : ''; ?>">
                        <input type="radio" name="<?php echo KWL_MAINT_OPTIONS; ?>[template]" value="portfolio" <?php checked($tpl,'portfolio'); ?>>
                        <div class="kwl-tpl-preview portfolio-preview">
                            <div class="tpl-badge">Ken Weill</div>
                            <div class="tpl-name">kenweill.com</div>
                            <div class="tpl-subhead">— thoughtfully curated —</div>
                            <div class="tpl-resume-btn">View resume →</div>
                        </div>
                        <span class="kwl-tpl-label">Personal / Portfolio</span>
                        <span class="kwl-tpl-desc">Best for personal sites. Adds a name badge, subheading, and a prominent resume/link button.</span>
                    </label>
                </div>
            </div>

            <!-- ===== TABS ===== -->
            <div class="kwl-tabs">
                <button type="button" class="kwl-tab active" data-tab="general">General</button>
                <button type="button" class="kwl-tab" data-tab="content-business" data-template="business">Business Content</button>
                <button type="button" class="kwl-tab" data-tab="content-portfolio" data-template="portfolio">Portfolio Content</button>
                <button type="button" class="kwl-tab" data-tab="colors">Colors</button>
                <button type="button" class="kwl-tab" data-tab="links">Contact Links</button>
                <button type="button" class="kwl-tab" data-tab="access">Access & SEO</button>
            </div>

            <!-- ===== GENERAL ===== -->
            <div class="kwl-panel active" id="tab-general">
                <div class="kwl-card">
                    <h2>🚦 Maintenance Mode</h2>
                    <label class="kwl-toggle-label">
                        <div class="kwl-toggle-switch">
                            <input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[enabled]" value="1" <?php checked($opts['enabled'],'1'); ?>>
                            <span class="kwl-slider"></span>
                        </div>
                        <span><?php echo $active ? '<strong>ON</strong> — Visitors see the maintenance page.' : '<strong>OFF</strong> — Site is live.'; ?></span>
                    </label>
                </div>

                <div class="kwl-card">
                    <h2>🏢 Identity</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Site / Company Name</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[site_name]" value="<?php echo esc_attr($opts['site_name']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>Page Icon</th>
                            <td>
                                <select name="<?php echo KWL_MAINT_OPTIONS; ?>[icon]" class="kwl-icon-select">
                                    <?php foreach ( $icons as $cls => $label ) : ?>
                                    <option value="<?php echo esc_attr($cls); ?>" <?php selected($opts['icon'],$cls); ?>><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="kwl-hint">Shown in the icon circle at the top</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="kwl-card">
                    <h2>📊 Progress Bar</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Show Progress Bar</th>
                            <td>
                                <label class="kwl-toggle-label">
                                    <div class="kwl-toggle-switch">
                                        <input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_progress]" value="1" <?php checked($opts['show_progress'],'1'); ?>>
                                        <span class="kwl-slider"></span>
                                    </div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>Progress % <span class="kwl-hint">(0–100)</span></th>
                            <td>
                                <input type="range" min="0" max="100"
                                    name="<?php echo KWL_MAINT_OPTIONS; ?>[progress_value]"
                                    value="<?php echo esc_attr($opts['progress_value']); ?>"
                                    class="kwl-range"
                                    oninput="document.getElementById('kwl-prog-val').textContent=this.value+'%'">
                                <span id="kwl-prog-val" class="kwl-range-val"><?php echo esc_html($opts['progress_value']); ?>%</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ===== BUSINESS CONTENT ===== -->
            <div class="kwl-panel" id="tab-content-business">
                <div class="kwl-card">
                    <h2>✏️ Business Template — Text</h2>
                    <p class="kwl-card-desc">These fields apply when the <strong>Business / Brand</strong> template is selected.</p>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Main Message / Tagline</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[tagline]" value="<?php echo esc_attr($opts['tagline']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>ETA / Status Text <span class="kwl-hint">(HTML ok)</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[eta_text]" value="<?php echo esc_attr($opts['eta_text']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Status Badge Text</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[status_badge_text]" value="<?php echo esc_attr($opts['status_badge_text']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Support Note</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[support_note]" value="<?php echo esc_attr($opts['support_note']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Footer Text</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[footer_note]" value="<?php echo esc_attr($opts['footer_note']); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ===== PORTFOLIO CONTENT ===== -->
            <div class="kwl-panel" id="tab-content-portfolio">
                <div class="kwl-card">
                    <h2>✏️ Portfolio Template — Text</h2>
                    <p class="kwl-card-desc">These fields apply when the <strong>Personal / Portfolio</strong> template is selected.</p>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Name Badge <span class="kwl-hint">(e.g. "Ken Weill")</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_name_badge]" value="<?php echo esc_attr($opts['portfolio_name_badge']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>Subheading <span class="kwl-hint">(e.g. "— thoughtfully curated —")</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_subhead]" value="<?php echo esc_attr($opts['portfolio_subhead']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>Main Message</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_tagline]" value="<?php echo esc_attr($opts['portfolio_tagline']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>ETA / Status Text <span class="kwl-hint">(HTML ok)</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_eta_text]" value="<?php echo esc_attr($opts['portfolio_eta_text']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Status Badge Text</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_status_badge]" value="<?php echo esc_attr($opts['portfolio_status_badge']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Footer Text</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[portfolio_footer_note]" value="<?php echo esc_attr($opts['portfolio_footer_note']); ?>" class="large-text"></td>
                        </tr>
                    </table>
                </div>

                <div class="kwl-card">
                    <h2>🔗 Custom Link Block <span class="kwl-hint">(e.g. Resume, Portfolio, Project)</span></h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Show Link Block</th>
                            <td>
                                <label class="kwl-toggle-label">
                                    <div class="kwl-toggle-switch">
                                        <input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_custom_link]" value="1" <?php checked($opts['show_custom_link'],'1'); ?>>
                                        <span class="kwl-slider"></span>
                                    </div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>Intro Text <span class="kwl-hint">(above the button)</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[custom_link_intro]" value="<?php echo esc_attr($opts['custom_link_intro']); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th>Button Label</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[custom_link_label]" value="<?php echo esc_attr($opts['custom_link_label']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>Button URL</th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[custom_link_url]" value="<?php echo esc_attr($opts['custom_link_url']); ?>" class="regular-text" placeholder="/resume or https://..."></td>
                        </tr>
                        <tr>
                            <th>Button Icon <span class="kwl-hint">(Font Awesome class)</span></th>
                            <td><input type="text" name="<?php echo KWL_MAINT_OPTIONS; ?>[custom_link_icon]" value="<?php echo esc_attr($opts['custom_link_icon']); ?>" class="regular-text" placeholder="fa-external-link-alt"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ===== COLORS ===== -->
            <div class="kwl-panel" id="tab-colors">
                <div class="kwl-card">
                    <h2>🎨 Business Template — Colors</h2>
                    <table class="form-table kwl-form-table">
                        <?php
                        $biz_colors = [
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
                        foreach ( $biz_colors as $key => $label ) : ?>
                        <tr>
                            <th><?php echo esc_html($label); ?></th>
                            <td><?php kwl_color_field($key, $opts[$key]); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <div class="kwl-card">
                    <h2>🎨 Portfolio Template — Colors</h2>
                    <table class="form-table kwl-form-table">
                        <?php
                        $port_colors = [
                            'portfolio_color_bg_from'       => 'Background Gradient — Start',
                            'portfolio_color_bg_to'         => 'Background Gradient — End',
                            'portfolio_color_title'         => 'Title Gradient — Start',
                            'portfolio_color_title_to'      => 'Title Gradient — End',
                            'portfolio_color_progress_from' => 'Progress Bar — Start',
                            'portfolio_color_progress_to'   => 'Progress Bar — End',
                            'portfolio_color_icon'          => 'Icon Color',
                            'portfolio_color_link_btn_bg'   => 'Link Button Background',
                            'portfolio_color_link_btn_text' => 'Link Button Text',
                        ];
                        foreach ( $port_colors as $key => $label ) : ?>
                        <tr>
                            <th><?php echo esc_html($label); ?></th>
                            <td><?php kwl_color_field($key, $opts[$key]); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- ===== LINKS ===== -->
            <div class="kwl-panel" id="tab-links">
                <div class="kwl-card">
                    <h2>🔗 Contact & Social Links <span class="kwl-hint">(Business template)</span></h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Support Email</th>
                            <td>
                                <input type="email" name="<?php echo KWL_MAINT_OPTIONS; ?>[support_email]" value="<?php echo esc_attr($opts['support_email']); ?>" class="regular-text">
                                <label class="kwl-inline-check"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_email_link]" value="1" <?php checked($opts['show_email_link'],'1'); ?>> Show</label>
                            </td>
                        </tr>
                        <tr>
                            <th>Facebook Page URL</th>
                            <td>
                                <input type="url" name="<?php echo KWL_MAINT_OPTIONS; ?>[facebook_url]" value="<?php echo esc_attr($opts['facebook_url']); ?>" class="regular-text">
                                <label class="kwl-inline-check"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_fb_link]" value="1" <?php checked($opts['show_fb_link'],'1'); ?>> Show</label>
                            </td>
                        </tr>
                        <tr>
                            <th>"Check Status" Button</th>
                            <td><label class="kwl-inline-check"><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[show_status_btn]" value="1" <?php checked($opts['show_status_btn'],'1'); ?>> Show</label></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ===== ACCESS ===== -->
            <div class="kwl-panel" id="tab-access">
                <div class="kwl-card">
                    <h2>🔐 Bypass Roles</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Administrators</th>
                            <td><label><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[bypass_admins]" value="1" <?php checked($opts['bypass_admins'],'1'); ?>> Always see the live site</label></td>
                        </tr>
                        <tr>
                            <th>Editors</th>
                            <td><label><input type="checkbox" name="<?php echo KWL_MAINT_OPTIONS; ?>[bypass_editors]" value="1" <?php checked($opts['bypass_editors'],'1'); ?>> Can bypass maintenance mode</label></td>
                        </tr>
                    </table>
                </div>
                <div class="kwl-card">
                    <h2>🤖 SEO</h2>
                    <table class="form-table kwl-form-table">
                        <tr>
                            <th>Robots Meta Tag</th>
                            <td>
                                <select name="<?php echo KWL_MAINT_OPTIONS; ?>[meta_robots]">
                                    <option value="noindex, nofollow" <?php selected($opts['meta_robots'],'noindex, nofollow'); ?>>noindex, nofollow (recommended)</option>
                                    <option value="noindex, follow"   <?php selected($opts['meta_robots'],'noindex, follow'); ?>>noindex, follow</option>
                                    <option value="index, follow"     <?php selected($opts['meta_robots'],'index, follow'); ?>>index, follow</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
                <?php submit_button('Save Settings', 'primary large', 'submit', false); ?>
                <a href="<?php echo esc_url(add_query_arg('kwl_preview','1',home_url('/'))); ?>" target="_blank" class="button button-secondary button-large">👁 Preview Page</a>
            </div>

        </form>
    </div>

    <?php kwl_maint_admin_styles(); ?>
    <?php kwl_maint_admin_scripts(); ?>
    <?php
}

/* ---------------------------------------------------------------
   HELPER: COLOR FIELD
--------------------------------------------------------------- */
function kwl_color_field( $key, $value ) {
    printf(
        '<input type="color" name="%1$s[%2$s]" value="%3$s" class="kwl-color-input" data-key="%2$s">
         <input type="text" class="kwl-hex-text" value="%3$s" readonly>',
        KWL_MAINT_OPTIONS,
        esc_attr($key),
        esc_attr($value)
    );
}

/* ---------------------------------------------------------------
   ADMIN STYLES
--------------------------------------------------------------- */
function kwl_maint_admin_styles() { ?>
<style>
.kwl-admin-wrap { max-width: 940px; }
.kwl-desc { color:#666; margin-bottom:20px; }
.kwl-card-desc { color:#777; font-size:13px; margin:-8px 0 14px; }
.kwl-status-pill { font-size:12px; padding:4px 12px; border-radius:20px; font-weight:600; }
.kwl-status-pill.active { background:#d4edda; color:#155724; }
.kwl-status-pill.inactive { background:#e2e3e5; color:#383d41; }
.kwl-version-pill { font-size:11px; padding:3px 10px; border-radius:20px; background:#f0f0f0; color:#666; font-weight:500; }

/* Template switcher */
.kwl-template-switcher { margin-bottom:20px; }
.kwl-template-row { display:flex; gap:16px; flex-wrap:wrap; margin-top:12px; }
.kwl-template-card { flex:1; min-width:220px; border:2px solid #e0e0e0; border-radius:12px; padding:16px; cursor:pointer; transition:all .2s; display:flex; flex-direction:column; gap:8px; }
.kwl-template-card input[type=radio] { display:none; }
.kwl-template-card:hover { border-color:#a0b4c8; }
.kwl-template-card.selected { border-color:#2271b1; background:#f0f6fc; }
.kwl-tpl-preview { border-radius:10px; padding:14px 12px; text-align:center; min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; }
.business-preview { background:linear-gradient(135deg,#f5f7fc,#eef2f8); }
.portfolio-preview { background:linear-gradient(135deg,#f8f9fc,#f0f2f8); }
.tpl-icon { font-size:1.8rem; }
.tpl-name { font-weight:700; font-size:.95rem; color:#1a2a3f; }
.tpl-bar { width:80%; height:5px; background:#e2e8f0; border-radius:10px; overflow:hidden; }
.tpl-fill { width:68%; height:100%; background:linear-gradient(90deg,#2c7cb6,#4f9fda); border-radius:10px; }
.tpl-badge { font-size:.65rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#6c7a8e; background:#eef2f8; padding:2px 10px; border-radius:20px; }
.tpl-subhead { font-size:.7rem; color:#6b7a8c; }
.tpl-resume-btn { font-size:.7rem; background:#1e2f40; color:#fff; padding:4px 12px; border-radius:20px; }
.kwl-tpl-label { font-weight:600; font-size:14px; color:#1d2327; }
.kwl-tpl-desc { font-size:12px; color:#666; line-height:1.4; }

/* Tabs */
.kwl-tabs { display:flex; gap:4px; border-bottom:2px solid #e0e0e0; margin-bottom:20px; flex-wrap:wrap; }
.kwl-tab { background:none; border:none; padding:10px 16px; cursor:pointer; font-size:13px; font-weight:500; color:#555; border-bottom:2px solid transparent; margin-bottom:-2px; border-radius:4px 4px 0 0; transition:all .15s; }
.kwl-tab:hover { background:#f5f5f5; color:#1d2327; }
.kwl-tab.active { background:#fff; color:#2271b1; border-bottom-color:#2271b1; border:1px solid #e0e0e0; border-bottom:2px solid #fff; }
.kwl-tab.dimmed { opacity:.5; }

/* Cards */
.kwl-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px 24px; margin-bottom:16px; }
.kwl-card h2 { font-size:15px; margin:0 0 16px; padding:0; border:none; }
.kwl-form-table th { width:240px; padding:10px 0; font-weight:500; vertical-align:middle; }
.kwl-form-table td { padding:8px 0; vertical-align:middle; }
.kwl-hint { font-weight:400; color:#888; font-size:11px; }

/* Toggle */
.kwl-toggle-label { display:flex; align-items:center; gap:12px; cursor:pointer; }
.kwl-toggle-switch { position:relative; width:44px; height:24px; flex-shrink:0; }
.kwl-toggle-switch input { opacity:0; width:0; height:0; }
.kwl-slider { position:absolute; top:0; left:0; right:0; bottom:0; background:#ccc; border-radius:24px; transition:.3s; cursor:pointer; }
.kwl-slider:before { content:''; position:absolute; height:18px; width:18px; left:3px; bottom:3px; background:white; border-radius:50%; transition:.3s; }
input:checked + .kwl-slider { background:#2271b1; }
input:checked + .kwl-slider:before { transform:translateX(20px); }

/* Range */
.kwl-range { width:220px; vertical-align:middle; }
.kwl-range-val { display:inline-block; min-width:36px; font-weight:600; color:#2271b1; margin-left:8px; }

/* Color */
.kwl-color-input { width:44px; height:32px; border:1px solid #ddd; border-radius:4px; padding:2px; cursor:pointer; vertical-align:middle; }
.kwl-hex-text { width:80px; margin-left:8px; font-family:monospace; font-size:12px; vertical-align:middle; }
.kwl-inline-check { margin-left:10px; font-size:13px; color:#555; }
.kwl-icon-select { min-width:180px; }
</style>
<?php }

/* ---------------------------------------------------------------
   ADMIN SCRIPTS
--------------------------------------------------------------- */
function kwl_maint_admin_scripts() { ?>
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

    // Template card selection
    document.querySelectorAll('.kwl-template-card').forEach(function(card) {
        card.addEventListener('click', function() {
            document.querySelectorAll('.kwl-template-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            card.querySelector('input[type=radio]').checked = true;
        });
    });

    // Color picker sync
    document.querySelectorAll('.kwl-color-input').forEach(function(picker) {
        picker.addEventListener('input', function() {
            picker.nextElementSibling.value = picker.value;
        });
    });
});
</script>
<?php }

/* ---------------------------------------------------------------
   FRONT-END INTERCEPT
--------------------------------------------------------------- */
add_action('template_redirect', function() {
    $opts = kwl_maint_options();

    $is_preview = isset($_GET['kwl_preview']) && current_user_can('manage_options');

    if ( $opts['enabled'] !== '1' && ! $is_preview ) return;

    if ( ! $is_preview ) {
        if ( is_user_logged_in() ) {
            if ( $opts['bypass_admins'] === '1' && current_user_can('manage_options') ) return;
            if ( $opts['bypass_editors'] === '1' && current_user_can('edit_others_posts') ) return;
        }
    }

    if ( is_admin() || $GLOBALS['pagenow'] === 'wp-login.php' ) return;

    if ( $opts['template'] === 'portfolio' ) {
        kwl_maint_render_portfolio($opts);
    } else {
        kwl_maint_render_business($opts);
    }
    exit;
});

/* ---------------------------------------------------------------
   SHARED HEAD / FOOT HELPERS
--------------------------------------------------------------- */
function kwl_maint_head( $title, $robots, $extra_css = '' ) {
    status_header(503);
    header('Retry-After: 3600');
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=yes">';
    echo '<meta name="robots" content="' . esc_attr($robots) . '">';
    echo '<title>' . esc_html($title) . '</title>';
    echo '<link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800|Raleway:300,400,500,600,700,800" rel="stylesheet">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">';
    echo '<style>' . $extra_css . '</style>';
    echo '</head><body>';
}

function kwl_maint_foot() {
    echo '</body></html>';
}

function kwl_maint_check_status_js( $name = '' ) {
    $msg = $name ? "— {$name} is working on improvements. Please revisit soon!" : "— site construction in progress.";
    return <<<JS
<script>
(function(){
    document.getElementById('kwl-year').textContent = new Date().getFullYear();
    var btn = document.getElementById('kwl-refresh');
    if(btn){
        btn.addEventListener('click',function(e){
            e.preventDefault();
            var t = new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
            var toast = document.querySelector('.kwl-toast');
            if(!toast){
                toast = document.createElement('div');
                toast.className='kwl-toast';
                Object.assign(toast.style,{position:'fixed',bottom:'20px',left:'50%',transform:'translateX(-50%)',background:'#1e2a3a',color:'white',padding:'8px 20px',borderRadius:'40px',fontSize:'.8rem',fontWeight:'500',boxShadow:'0 4px 12px rgba(0,0,0,.2)',zIndex:'999',transition:'opacity .4s',fontFamily:"'Inter',sans-serif",pointerEvents:'none'});
                document.body.appendChild(toast);
            }
            toast.innerHTML='<i class="fas fa-check-circle" style="margin-right:6px"></i>Last check: '+t+' $msg';
            toast.style.opacity='1';
            setTimeout(function(){toast.style.opacity='0';setTimeout(function(){if(toast.parentNode)toast.remove();},500);},2800);
        });
    }
})();
</script>
JS;
}

/* ---------------------------------------------------------------
   TEMPLATE 1 — BUSINESS
--------------------------------------------------------------- */
function kwl_maint_render_business( $opts ) {
    $progress = intval($opts['progress_value']);
    $p_max    = min(100, $progress + 6);
    $p_min    = max(0,   $progress - 4);

    $css = "
:root{
    --bg-from:       {$opts['color_bg_from']};
    --bg-to:         {$opts['color_bg_to']};
    --card-bg:       {$opts['color_card_bg']};
    --title-from:    {$opts['color_title']};
    --title-to:      {$opts['color_title_to']};
    --body-text:     {$opts['color_body_text']};
    --prog-from:     {$opts['color_progress_from']};
    --prog-to:       {$opts['color_progress_to']};
    --icon-color:    {$opts['color_icon']};
    --link-text:     {$opts['color_link']};
    --link-bg:       {$opts['color_link_bg']};
}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:linear-gradient(135deg,var(--bg-from) 0%,var(--bg-to) 100%);font-family:'Inter','Raleway',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;position:relative;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:radial-gradient(circle at 20% 40%,rgba(99,102,241,.03) 0%,rgba(0,0,0,0) 70%);pointer-events:none;z-index:0;}
.maintenance-card{max-width:620px;width:100%;background:var(--card-bg);border-radius:48px;box-shadow:0 25px 45px -12px rgba(0,0,0,.2),0 4px 12px rgba(0,0,0,.03),inset 0 1px 0 rgba(255,255,255,.8);padding:2.5rem 2rem 3rem;text-align:center;transition:transform .25s ease,box-shadow .3s ease;border:1px solid rgba(255,255,255,.6);position:relative;z-index:2;}
.maintenance-card:hover{transform:translateY(-4px);box-shadow:0 32px 55px -14px rgba(0,0,0,.25);}
.icon-wrapper{background:linear-gradient(145deg,#f0f3fe,#e9eef9);width:110px;height:110px;border-radius:60px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.8rem;box-shadow:inset 0 1px 3px rgba(0,0,0,.02),0 12px 20px -8px rgba(0,0,0,.1);border:1px solid rgba(255,255,255,.7);}
.icon-wrapper i{font-size:3.8rem;color:var(--icon-color);filter:drop-shadow(0 2px 4px rgba(0,0,0,.05));transition:transform .3s ease;}
.icon-wrapper:hover i{transform:rotate(12deg) scale(1.05);}
h1{font-size:2.4rem;font-weight:800;background:linear-gradient(130deg,var(--title-from),var(--title-to));background-clip:text;-webkit-background-clip:text;color:transparent;margin-bottom:1rem;letter-spacing:-.02em;}
.msg-text{font-size:1.18rem;line-height:1.5;color:var(--body-text);font-weight:450;background:rgba(235,245,255,.6);padding:1.2rem 1.5rem;border-radius:60px;margin:1.5rem 0 1rem;border:1px solid rgba(255,255,255,.8);box-shadow:inset 0 0 0 1px rgba(255,255,255,.7),0 2px 6px rgba(0,0,0,.02);}
.msg-text i{margin-right:8px;color:#3b6e9e;}
.support-note{font-size:.95rem;color:#5a6e84;background:rgba(255,255,255,.8);display:inline-flex;align-items:center;gap:8px;padding:.6rem 1.2rem;border-radius:100px;margin-top:1.5rem;border:1px solid #e2edf7;}
.progress-section{margin:2rem 0 1.2rem;}
.eta-message{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;font-size:.9rem;font-weight:500;color:#2c6280;background:#eef3fc;padding:.65rem 1rem;border-radius:100px;width:fit-content;margin:0 auto;}
.progress-track{background:#e2e8f0;border-radius:40px;height:8px;width:100%;margin:1rem 0 .4rem;overflow:hidden;box-shadow:inset 0 1px 2px rgba(0,0,0,.05);}
.progress-fill{width:{$progress}%;height:100%;background:linear-gradient(90deg,var(--prog-from),var(--prog-to));border-radius:40px;animation:pulseProgress 1.8s infinite ease;}
@keyframes pulseProgress{0%{opacity:.7;width:{$p_min}%;}50%{opacity:1;width:{$p_max}%;}100%{opacity:.7;width:{$p_min}%;}}
.status-badge{display:inline-flex;align-items:center;gap:6px;background:#fef9e3;padding:6px 14px;border-radius:50px;font-size:.75rem;font-weight:600;color:#b2601a;margin-top:12px;border:1px solid #ffe6b3;}
.contact-row{display:flex;justify-content:center;gap:28px;margin-top:2rem;flex-wrap:wrap;}
.contact-link{text-decoration:none;font-size:.85rem;font-weight:500;color:var(--link-text);background:var(--link-bg);padding:8px 18px;border-radius:40px;transition:all .2s ease;display:inline-flex;align-items:center;gap:8px;border:1px solid #dce5f0;}
.contact-link:hover{background:#e6edf6;color:#1f4a73;transform:scale(.97);border-color:#bfd2e6;}
.footer-note{font-size:.7rem;color:#8ba0b5;margin-top:2.2rem;border-top:1px dashed #dce5f0;padding-top:1.5rem;letter-spacing:.3px;}
@media(max-width:550px){.maintenance-card{padding:1.8rem 1.5rem 2rem;}h1{font-size:1.9rem;}.msg-text{font-size:1rem;padding:1rem;}.icon-wrapper{width:85px;height:85px;}.icon-wrapper i{font-size:2.8rem;}.contact-link{padding:6px 14px;font-size:.8rem;}}
";

    kwl_maint_head( esc_html($opts['site_name']) . ' • Under Construction', $opts['meta_robots'], $css );
    ?>
    <section class="maintenance-card">
        <div class="icon-wrapper">
            <i class="fas <?php echo esc_attr($opts['icon']); ?>"></i>
        </div>
        <h1><?php echo esc_html($opts['site_name']); ?></h1>
        <div class="msg-text">
            <i class="fas fa-hard-hat"></i>
            <?php echo esc_html($opts['tagline']); ?>
        </div>

        <?php if ( $opts['show_progress'] === '1' ) : ?>
        <div class="progress-section">
            <div class="eta-message">
                <i class="fas fa-hourglass-half"></i>
                <span><?php echo wp_kses_post($opts['eta_text']); ?></span>
            </div>
            <div class="progress-track"><div class="progress-fill"></div></div>
            <div class="status-badge">
                <i class="fas fa-sync-alt fa-fw"></i>
                <span><?php echo esc_html($opts['status_badge_text']); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="contact-row">
            <?php if ( $opts['show_email_link'] === '1' && ! empty($opts['support_email']) ) : ?>
            <a href="mailto:<?php echo esc_attr($opts['support_email']); ?>?subject=Maintenance%20Inquiry" class="contact-link">
                <i class="fas fa-envelope"></i> Support
            </a>
            <?php endif; ?>
            <?php if ( $opts['show_status_btn'] === '1' ) : ?>
            <a href="#" class="contact-link" id="kwl-refresh">
                <i class="fas fa-redo-alt"></i> Check status
            </a>
            <?php endif; ?>
            <?php if ( $opts['show_fb_link'] === '1' && ! empty($opts['facebook_url']) ) : ?>
            <a href="<?php echo esc_url($opts['facebook_url']); ?>" class="contact-link" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-facebook-f"></i> Updates
            </a>
            <?php endif; ?>
        </div>

        <?php if ( ! empty($opts['support_note']) ) : ?>
        <div class="support-note">
            <i class="fas fa-clock"></i>
            <span><?php echo esc_html($opts['support_note']); ?></span>
        </div>
        <?php endif; ?>

        <div class="footer-note">
            <i class="far fa-smile"></i>
            <?php echo esc_html($opts['footer_note']); ?> &bull; <span id="kwl-year"></span> <?php echo esc_html($opts['site_name']); ?>
        </div>
    </section>
    <?php
    echo kwl_maint_check_status_js();
    kwl_maint_foot();
}

/* ---------------------------------------------------------------
   TEMPLATE 2 — PORTFOLIO
--------------------------------------------------------------- */
function kwl_maint_render_portfolio( $opts ) {
    $progress = intval($opts['progress_value']);
    $p_max    = min(100, $progress + 6);
    $p_min    = max(0,   $progress - 4);

    $css = "
:root{
    --bg-from:    {$opts['portfolio_color_bg_from']};
    --bg-to:      {$opts['portfolio_color_bg_to']};
    --title-from: {$opts['portfolio_color_title']};
    --title-to:   {$opts['portfolio_color_title_to']};
    --prog-from:  {$opts['portfolio_color_progress_from']};
    --prog-to:    {$opts['portfolio_color_progress_to']};
    --icon-color: {$opts['portfolio_color_icon']};
    --btn-bg:     {$opts['portfolio_color_link_btn_bg']};
    --btn-text:   {$opts['portfolio_color_link_btn_text']};
}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:linear-gradient(135deg,var(--bg-from) 0%,var(--bg-to) 100%);font-family:'Inter','Raleway',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;position:relative;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:radial-gradient(circle at 20% 40%,rgba(79,70,229,.03) 0%,rgba(0,0,0,0) 70%);pointer-events:none;z-index:0;}
.maintenance-card{max-width:600px;width:100%;background:rgba(255,255,255,.96);border-radius:48px;box-shadow:0 25px 45px -12px rgba(0,0,0,.15),0 4px 12px rgba(0,0,0,.03),inset 0 1px 0 rgba(255,255,255,.8);padding:2.8rem 2rem 3rem;text-align:center;transition:transform .25s ease,box-shadow .3s ease;border:1px solid rgba(255,255,255,.6);position:relative;z-index:2;}
.maintenance-card:hover{transform:translateY(-4px);box-shadow:0 32px 55px -14px rgba(0,0,0,.2);}
.icon-wrapper{background:linear-gradient(145deg,#f0f2fe,#e8ecf9);width:110px;height:110px;border-radius:60px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.8rem;box-shadow:inset 0 1px 3px rgba(0,0,0,.02),0 12px 20px -8px rgba(0,0,0,.08);border:1px solid rgba(255,255,255,.7);}
.icon-wrapper i{font-size:3.8rem;color:var(--icon-color);filter:drop-shadow(0 2px 4px rgba(0,0,0,.05));transition:transform .2s ease;}
.icon-wrapper:hover i{transform:rotate(3deg) scale(1.05);}
.name-badge{font-size:.9rem;font-weight:600;letter-spacing:.3px;text-transform:uppercase;color:#6c7a8e;background:#eef2f8;display:inline-block;padding:.3rem 1rem;border-radius:40px;margin-bottom:1rem;}
h1{font-size:2.8rem;font-weight:700;background:linear-gradient(130deg,var(--title-from),var(--title-to));background-clip:text;-webkit-background-clip:text;color:transparent;margin-bottom:.75rem;letter-spacing:-.02em;}
.subhead{font-size:1rem;font-weight:400;color:#6b7a8c;margin-bottom:.5rem;}
.msg-text{font-size:1.18rem;line-height:1.5;color:#2c3e50;font-weight:450;background:rgba(235,245,255,.6);padding:1.2rem 1.5rem;border-radius:60px;margin:1.5rem 0 1rem;border:1px solid rgba(255,255,255,.8);}
.msg-text i{margin-right:8px;color:#3b6e9e;}
.resume-highlight{background:linear-gradient(135deg,#eef2ff,#e8edf9);border-radius:32px;padding:1.2rem 1.5rem;margin:1.5rem 0;border:1px solid rgba(79,70,229,.15);}
.resume-highlight p{font-size:.95rem;color:#2c3e50;margin-bottom:.8rem;font-weight:500;}
.resume-link{display:inline-flex;align-items:center;gap:10px;background:var(--btn-bg);color:var(--btn-text);text-decoration:none;padding:.75rem 1.8rem;border-radius:60px;font-weight:600;font-size:.95rem;transition:all .2s ease;box-shadow:0 2px 6px rgba(0,0,0,.1);}
.resume-link:hover{opacity:.88;transform:scale(.98);gap:13px;}
.progress-section{margin:1rem 0 .5rem;}
.eta-message{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;font-size:.9rem;font-weight:500;color:#2c6280;background:#eef3fc;padding:.65rem 1rem;border-radius:100px;width:fit-content;margin:0 auto;}
.progress-track{background:#e2e8f0;border-radius:40px;height:8px;width:100%;margin:1rem 0 .4rem;overflow:hidden;}
.progress-fill{width:{$progress}%;height:100%;background:linear-gradient(90deg,var(--prog-from),var(--prog-to));border-radius:40px;animation:pulseProgress 1.8s infinite ease;}
@keyframes pulseProgress{0%{opacity:.7;width:{$p_min}%;}50%{opacity:1;width:{$p_max}%;}100%{opacity:.7;width:{$p_min}%;}}
.status-badge{display:inline-flex;align-items:center;gap:6px;background:#fef9e3;padding:6px 14px;border-radius:50px;font-size:.75rem;font-weight:600;color:#b2601a;margin-top:12px;border:1px solid #ffe6b3;}
.action-row{display:flex;justify-content:center;gap:20px;margin-top:1.8rem;flex-wrap:wrap;}
.action-link{text-decoration:none;font-size:.85rem;font-weight:500;color:#4a6f8f;background:#f0f4fa;padding:8px 20px;border-radius:40px;transition:all .2s ease;display:inline-flex;align-items:center;gap:8px;border:1px solid #dce5f0;}
.action-link:hover{background:#e6edf6;transform:scale(.97);}
.footer-note{font-size:.7rem;color:#8ba0b5;margin-top:2rem;border-top:1px dashed #dce5f0;padding-top:1.5rem;}
@media(max-width:550px){.maintenance-card{padding:1.8rem 1.5rem 2rem;}h1{font-size:2rem;}.icon-wrapper{width:85px;height:85px;}.icon-wrapper i{font-size:2.8rem;}.resume-link{padding:.6rem 1.4rem;font-size:.85rem;}}
";

    kwl_maint_head( esc_html($opts['portfolio_name_badge']) . ' • Site Maintenance', $opts['meta_robots'], $css );
    ?>
    <section class="maintenance-card">
        <div class="icon-wrapper">
            <i class="fas <?php echo esc_attr($opts['icon']); ?>"></i>
        </div>

        <?php if ( ! empty($opts['portfolio_name_badge']) ) : ?>
        <div class="name-badge">
            <i class="fas fa-user-astronaut"></i> <?php echo esc_html($opts['portfolio_name_badge']); ?>
        </div>
        <?php endif; ?>

        <h1><?php echo esc_html($opts['site_name']); ?></h1>

        <?php if ( ! empty($opts['portfolio_subhead']) ) : ?>
        <div class="subhead"><?php echo esc_html($opts['portfolio_subhead']); ?></div>
        <?php endif; ?>

        <div class="msg-text">
            <i class="fas fa-paintbrush"></i>
            <?php echo esc_html($opts['portfolio_tagline']); ?>
        </div>

        <?php if ( $opts['show_custom_link'] === '1' && ! empty($opts['custom_link_url']) ) : ?>
        <div class="resume-highlight">
            <?php if ( ! empty($opts['custom_link_intro']) ) : ?>
            <p><i class="fas fa-file-alt"></i> <?php echo esc_html($opts['custom_link_intro']); ?></p>
            <?php endif; ?>
            <a href="<?php echo esc_url($opts['custom_link_url']); ?>" class="resume-link" target="_blank" rel="noopener noreferrer">
                <i class="fas <?php echo esc_attr($opts['custom_link_icon']); ?>"></i>
                <?php echo esc_html($opts['custom_link_label']); ?>
            </a>
        </div>
        <?php endif; ?>

        <?php if ( $opts['show_progress'] === '1' ) : ?>
        <div class="progress-section">
            <div class="eta-message">
                <i class="fas fa-sparkles"></i>
                <span><?php echo wp_kses_post($opts['portfolio_eta_text']); ?></span>
            </div>
            <div class="progress-track"><div class="progress-fill"></div></div>
            <div class="status-badge">
                <i class="fas fa-mug-hot fa-fw"></i>
                <span><?php echo esc_html($opts['portfolio_status_badge']); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="action-row">
            <a href="#" class="action-link" id="kwl-refresh">
                <i class="fas fa-rotate-right"></i> Check status
            </a>
        </div>

        <div class="footer-note">
            <i class="far fa-clock"></i>
            <?php echo esc_html($opts['portfolio_footer_note']); ?> &bull; <span id="kwl-year"></span> <?php echo esc_html($opts['portfolio_name_badge'] ?: $opts['site_name']); ?>
        </div>
    </section>
    <?php
    echo kwl_maint_check_status_js( esc_js($opts['portfolio_name_badge']) );
    kwl_maint_foot();
}
