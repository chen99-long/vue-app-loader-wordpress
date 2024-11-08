<?php
/*
Plugin Name: Vue App Loader
Description: 通过短代码加载远程Vue应用
Version: 1.1
Author: CHENLONG

/*
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// 防止直接访问此文件
if (!defined('ABSPATH')) {
    exit;
}

// 注册短代码
function vue_app_loader_shortcode($atts) {
    // 获取属性，设置默认值
    $atts = shortcode_atts(array(
        'cdn' => 'https://cdn.vimego.io/ai_tools/video-denoise/ai_tools/Writer/',
        'version' => '',              // 添加版本号属性
        'width' => '100%',            // 添加宽度属性
        'height' => 'auto',           // 添加高度属性
        'margin' => '0',              // 添加边距属性
        'padding' => '0',             // 添加内边距属性
        'class' => '',                // 添加自定义类名属性
        'style' => ''                 // 添加自定义样式属性
    ), $atts);

    // 加载必要的JS
    wp_enqueue_script('vue-app-loader', plugins_url('js/loader.js', __FILE__), array(), '1.0', true);
    
    // 传递CDN URL到JavaScript
    wp_localize_script('vue-app-loader', 'vueAppLoader', array(
        'cdnUrl' => $atts['cdn']
    ));

    // 构建内联样式
    $style = sprintf(
        'width: %s; height: %s; margin: %s; padding: %s; %s',
        esc_attr($atts['width']),
        esc_attr($atts['height']),
        esc_attr($atts['margin']),
        esc_attr($atts['padding']),
        esc_attr($atts['style'])
    );

    // 返回带样式的挂载点
    return sprintf(
        '<div id="app" class="%s" style="%s"></div>',
        esc_attr($atts['class']),
        $style
    );
}
add_shortcode('vue_app', 'vue_app_loader_shortcode');

// 添加自定义CSS
function vue_app_loader_styles() {
    ?>
    <style>
        /* 默认样式 */
        #app {
            display: block;
            box-sizing: border-box;
        }
        /* 可以添加更多默认样式 */
    </style>
    <?php
}
add_action('wp_head', 'vue_app_loader_styles');
