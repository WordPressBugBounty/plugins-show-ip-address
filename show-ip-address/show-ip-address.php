<?php
/*
 * Plugin Name: Show IP address
 * Plugin URI: https://www.keithgriffiths.co.uk
 * Description: A simple plugin to show your IP address information on any of your pages, posts or widgets. Shows your IP address on your Dashboard.
 * Version: 1.8
 * Author: Keith Griffiths
 * Author URI: https://www.keithgriffiths.co.uk
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/* Copyright 2015 to 2025 Keith Griffiths (email : info@keithgriffiths.co.uk)
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2,  
   as published by the Free Software Foundation.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
   See the GNU General Public License for more details.
*/

// Enqueue optional CSS
function show_ip_enqueue_styles() {
    wp_enqueue_style('custom-style', plugins_url('css/style-show-ip-address.css', __FILE__), array(), 'all');
}
add_action('wp_enqueue_scripts', 'show_ip_enqueue_styles');

// Admin menu page
add_action('admin_menu', 'ip_plugin_settings');
function ip_plugin_settings() {
    add_menu_page('IP Address Settings', 'Show IP Info', 'administrator', 'show-ip-address', 'ip_address_display_settings');
}

function ip_address_display_settings() {
    echo '<h1>Show IP Address</h1>';
    echo '<div>A simple plugin to show your IP address on any of your pages, posts or widgets. <br>Shows your IP address on your dashboard from any location.</div><hr>';
    echo '<div>The idea is simple, give it a go, let me know what you think of this plugin. Any suggested updates <br>I\'ll consider in each build <a href="https://www.keithgriffiths.co.uk/contact/">click here</a> to contact me anytime.</div>';

    echo '<h2>Use on pages, widgets or posts</h2><hr>';
    echo '<div><h3>Shortcodes:</h3>
    <ul>
        <li><code>[show_ip]</code> – Display visitor IP address</li>
        <li><code>[show_ip_hostname]</code> – Display IP hostname</li>
        <li><code>[show_ip_useragent]</code> – Display browser user agent</li>
        <li><code>[show_ip_referrer]</code> – Display referrer URL</li>
    </ul></div><hr>';

    echo '<h3>If you find this plugin useful, consider buying me a coffee 😊</h3>';
    echo '<img src="' . plugins_url('images/buymeacoffee-qr.png', __FILE__) . '" alt="Donate QR" style="width:200px; height:auto;">';

    echo '<br><br><br><br>Thank you for using my plugin - Show IP address V.1.8 - Last Updated: 08-08-2025';
}

// Dashboard widget
if (is_admin()) {
    function ip_dashboard_widget_function() {
        $admin_ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $admin_hostname = @gethostbyaddr($admin_ip_address);
        if (!$admin_hostname || $admin_hostname === $admin_ip_address) {
            $admin_hostname = 'Not known';
        }

        echo '<div style="display:table; width: 100%;">';
        echo '<div style="display:table-cell; text-align: right;"><small>(' . __('hostname', 'admin-ip-address') . ' : ' . esc_html($admin_hostname) . ')</small></div>';
        echo '</div>';
        echo '<div class="box-ip"><hr>Your IP address is something you might rarely think about, but it\'s important to know what your IP address is and when it changes. <hr> Want to know more about IP addresses, or get the latest updates on this plugin? Go to "Show IP Info" in the menu. <hr>Show IP address. Latest version 1.8</div>';
    }

    function ip_add_dashboard_widgets() {
        wp_add_dashboard_widget('ip_dashboard_widget', __('Your IP Address & Hostname', 'admin-ip-address'), 'ip_dashboard_widget_function');
    }

    add_action('wp_dashboard_setup', 'ip_add_dashboard_widgets');
}

// Add contact link on plugin list
if (is_admin()) {
    function user_ip_contact_link($links) {
        $contact_link = '<a href="https://www.keithgriffiths.co.uk/" target="_blank">Contact Me</a>';
        array_push($links, $contact_link);
        return $links;
    }

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", 'user_ip_contact_link');
}

// IP helper
function get_the_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return apply_filters('wpb_get_ip', esc_html(trim($ip)));
}

// Shortcodes
function shortcode_show_ip() {
    return esc_html(get_the_user_ip());
}
add_shortcode('show_ip', 'shortcode_show_ip');

function shortcode_show_ip_hostname() {
    $ip = get_the_user_ip();
    $hostname = @gethostbyaddr($ip);
    if (!$hostname || $hostname === $ip) {
        $hostname = 'Hostname not available';
    }
    return esc_html("Hostname: {$hostname}");
}
add_shortcode('show_ip_hostname', 'shortcode_show_ip_hostname');

function shortcode_show_ip_useragent() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    return esc_html("User Agent: {$ua}");
}
add_shortcode('show_ip_useragent', 'shortcode_show_ip_useragent');

function shortcode_show_ip_referrer() {
    $ref = $_SERVER['HTTP_REFERER'] ?? 'No referrer';
    return esc_html("Referrer: {$ref}");
}
add_shortcode('show_ip_referrer', 'shortcode_show_ip_referrer');
?>