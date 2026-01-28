<?php
/**
 * Plugin Name:       EneaCodes FAQ Schema
 * Plugin URI:        https://github.com/EneaCodes/EneaCodes-FAQ-Schema
 * Description:       Add beautiful FAQ sections with automatic Schema.org markup for better SEO and Google rich snippets
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Enea
 * Author URI:        https://github.com/EneaCodes
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       eneacodes-faq-schema
 */

if (!defined('ABSPATH')) {
    exit;
}

class FAQ_Schema_Bros {
    
    public function __construct() {
        // Add meta box for FAQ
        add_action('add_meta_boxes', array($this, 'add_faq_meta_box'));
        add_action('save_post', array($this, 'save_faq_data'));
        
        // Add FAQ to content
        add_filter('the_content', array($this, 'add_faq_to_content'), 15);
        
        // Add styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // Add Schema.org markup to head
        add_action('wp_head', array($this, 'add_faq_schema_markup'));
        
        // Load text domain for translations (fixed - removed load_plugin_textdomain warning)
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }
    
    // Load plugin text domain - FIXED VERSION
    public function load_textdomain() {
        // WordPress.org hosted plugins handle translations automatically
        // For non-WordPress.org installations, we can still support translations
        // This is the modern way to handle plugin translations
        $locale = apply_filters('eneacodes_faq_schema_locale', determine_locale(), 'eneacodes-faq-schema');
        $mofile = WP_LANG_DIR . '/plugins/eneacodes-faq-schema-' . $locale . '.mo';
        
        if (file_exists($mofile)) {
            load_textdomain('eneacodes-faq-schema', $mofile);
        }
    }
    
    // Add Meta Box
    public function add_faq_meta_box() {
        // Only show meta box to users who can edit posts
        if (!current_user_can('edit_posts')) {
            return;
        }
        
        add_meta_box(
            'faq_schema_box',
            '❓ EneaCodes FAQ Schema (for Google Rich Snippets)',
            array($this, 'render_faq_meta_box'),
            'post',
            'normal',
            'high'
        );
    }
    
    // Render FAQ Meta Box
    public function render_faq_meta_box($post) {
        wp_nonce_field('faq_schema_nonce', 'faq_schema_nonce');
        
        // Get saved FAQs
        $faqs = get_post_meta($post->ID, '_faq_items', true);
        if (!is_array($faqs)) {
            $faqs = array();
        }
        
        // Get display position
        $position = get_post_meta($post->ID, '_faq_position', true);
        if (empty($position)) {
            $position = 'bottom';
        }
        
        ?>
        <div class="faq-schema-wrapper">
            
            <!-- Header with Stats -->
            <div class="faq-header">
                <div class="faq-header-left">
                    <div class="faq-icon-badge">❓</div>
                    <div>
                        <h3>FAQ Schema Bros</h3>
                        <p>Boost SEO with Google Rich Snippets</p>
                    </div>
                </div>
                <div class="faq-header-right">
                    <div class="faq-stat">
                        <span class="faq-stat-number"><?php echo esc_html(count($faqs)); ?></span>
                        <span class="faq-stat-label">FAQs</span>
                    </div>
                </div>
            </div>
            
            <!-- Benefits Cards -->
            <div class="faq-benefits">
                <div class="faq-benefit-card">
                    <div class="benefit-icon">🎯</div>
                    <div class="benefit-text">
                        <strong>Rich Snippets</strong>
                        <span>Appear in Google with dropdown FAQs</span>
                    </div>
                </div>
                <div class="faq-benefit-card">
                    <div class="benefit-icon">📈</div>
                    <div class="benefit-text">
                        <strong>Higher CTR</strong>
                        <span>3x more clicks from search results</span>
                    </div>
                </div>
                <div class="faq-benefit-card">
                    <div class="benefit-icon">🗣️</div>
                    <div class="benefit-text">
                        <strong>Voice Search</strong>
                        <span>Optimized for Alexa & Google Assistant</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Tips -->
            <div class="faq-tips-section">
                <div class="faq-tips-header">💡 Quick Tips for Posts</div>
                <div class="faq-tips-grid">
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Is parking available?'); ?>">Is parking available?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Sandy or pebbles?'); ?>">Sandy or pebbles?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Shallow water?'); ?>">Shallow water?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Windy or calm?'); ?>">Windy or calm?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Sunbeds & umbrellas?'); ?>">Sunbeds & umbrellas?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Free or paid?'); ?>">Free or paid?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Good for families?'); ?>">Good for families?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Crowded in summer?'); ?>">Crowded in summer?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Beach bar / food nearby?'); ?>">Beach bar / food nearby?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('WC / showers available?'); ?>">WC / showers available?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Good for snorkeling?'); ?>">Good for snorkeling?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Best time to visit?'); ?>">Best time to visit?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Good for sunset?'); ?>">Good for sunset?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('How to get there?'); ?>">How to get there?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Natural shade?'); ?>">Natural shade?</span>
			<span class="faq-tip-tag" data-question="<?php echo esc_attr('Lifeguard present?'); ?>">Lifeguard present?</span>
                </div>
            </div>
            
            <!-- Display Settings -->
            <div class="faq-settings-card">
                <div class="faq-setting-row">
                    <div class="faq-setting-label">
                        <span class="setting-icon">📍</span>
                        <div>
                            <strong>Display Position</strong>
                            <p>Where should FAQs appear on your post?</p>
                        </div>
                    </div>
                    <select name="faq_position" class="faq-select">
                        <option value="bottom" <?php selected($position, 'bottom'); ?>>✅ Bottom (Recommended)</option>
                        <option value="top" <?php selected($position, 'top'); ?>>⬆️ Top of Post</option>
                        <option value="hidden" <?php selected($position, 'hidden'); ?>>👁️ Hidden (Not Recommended)</option>
                    </select>
                </div>
                <div class="faq-recommendation">
                    <strong>⚠️ Important:</strong> Use "Bottom" for best SEO. Google prefers visible FAQs that help users. Hidden schema may be ignored or penalized.
                </div>
            </div>
            
            <!-- FAQ List -->
            <div class="faq-list-header">
                <h4>Your FAQs</h4>
                <button type="button" class="faq-add-btn-top" id="add-faq-btn-top">
                    <span class="btn-icon">➕</span>
                    Add FAQ
                </button>
            </div>
            
            <div class="faq-list" id="faq-list">
                <?php
                if (!empty($faqs)) {
                    foreach ($faqs as $index => $faq) {
                        $this->render_faq_item($index, $faq);
                    }
                } else {
                    // Show one empty FAQ by default
                    $this->render_faq_item(0, array('question' => '', 'answer' => ''));
                }
                ?>
            </div>
            
            <!-- Add FAQ Button -->
            <button type="button" class="faq-add-button" id="add-faq-btn">
                <span class="btn-icon">➕</span>
                Add Another FAQ
            </button>
            
        </div>
        <?php
    }
    
    // Render Single FAQ Item
    private function render_faq_item($index, $faq) {
        $question = isset($faq['question']) ? $faq['question'] : '';
        $answer   = isset($faq['answer'])   ? $faq['answer']   : '';
        ?>
        <div class="faq-item-modern" data-index="<?php echo esc_attr($index); ?>">
            <div class="faq-item-header-modern">
                <div class="faq-drag-handle" title="Drag to reorder">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                </div>
                <div class="faq-item-number">#<?php echo esc_html($index + 1); ?></div>
                <div class="faq-item-title"><?php echo esc_html($question ? $question : 'New FAQ'); ?></div>
                <button type="button" class="faq-delete-btn" title="Delete FAQ">
                    <span>🗑️</span>
                </button>
            </div>
            
            <div class="faq-item-body">
                <div class="faq-input-group">
                    <label class="faq-label">
                        <span class="label-icon">❓</span>
                        Question
                    </label>
                    <input type="text" 
                           name="faq_items[<?php echo esc_attr($index); ?>][question]" 
                           value="<?php echo esc_attr($question); ?>" 
                           placeholder="e.g., Is parking available at this beach?"
                           class="faq-input">
                </div>
                
                <div class="faq-input-group">
                    <label class="faq-label">
                        <span class="label-icon">💬</span>
                        Answer
                    </label>
                    <textarea name="faq_items[<?php echo esc_attr($index); ?>][answer]" 
                              rows="3" 
                              class="faq-textarea"
                              placeholder="e.g., Yes, there is free parking available about 100 meters from the beach entrance."><?php echo esc_textarea($answer); ?></textarea>
                    <div class="faq-char-count">
                        <span class="char-current"><?php echo esc_html(mb_strlen($answer)); ?></span> / <span class="char-max">500</span> characters
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    // Save FAQ Data
    public function save_faq_data($post_id) {
        // FIXED: Properly sanitize nonce before verification
        if (!isset($_POST['faq_schema_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['faq_schema_nonce'])), 'faq_schema_nonce')) {
            return;
        }
        
        // Check if it's an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Get FAQ items from POST - sanitize at access point to satisfy Plugin Check
        $faq_items_raw = array();
        if (isset($_POST['faq_items']) && is_array($_POST['faq_items'])) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in loop below
            $faq_items_raw = wp_unslash($_POST['faq_items']);
        }
        
        // Process and sanitize FAQs
        $faq_items = array();
        if (is_array($faq_items_raw)) {
            foreach ($faq_items_raw as $item) {
                // Validate that item is an array with required keys
                if (!is_array($item) || empty($item['question']) || empty($item['answer'])) {
                    continue;
                }
                
                $faq_items[] = array(
                    'question' => sanitize_text_field($item['question']),
                    'answer'   => wp_kses_post($item['answer'])
                );
            }
        }
        
        // Save FAQs
        if (!empty($faq_items)) {
            update_post_meta($post_id, '_faq_items', $faq_items);
        } else {
            delete_post_meta($post_id, '_faq_items');
        }
        
        // Save position setting
        if (isset($_POST['faq_position'])) {
            $position = sanitize_text_field(wp_unslash($_POST['faq_position']));
            $allowed_positions = array('top', 'bottom', 'hidden');
            if (in_array($position, $allowed_positions, true)) {
                update_post_meta($post_id, '_faq_position', $position);
            }
        }
    }
    
    // Add FAQs to Post Content
    public function add_faq_to_content($content) {
        // Only on single posts
        if (!is_singular('post')) {
            return $content;
        }
        
        global $post;
        $faqs = get_post_meta($post->ID, '_faq_items', true);
        
        // No FAQs? Return original content
        if (empty($faqs) || !is_array($faqs)) {
            return $content;
        }
        
        // Get position setting
        $position = get_post_meta($post->ID, '_faq_position', true);
        if (empty($position)) {
            $position = 'bottom';
        }
        
        // Don't show if hidden
        if ($position === 'hidden') {
            return $content;
        }
        
        // Build FAQ HTML
        $faq_html = '<div class="faq-schema-section">';
        $faq_html .= '<h2 class="faq-schema-title">❓ Frequently Asked Questions</h2>';
        $faq_html .= '<div class="faq-schema-list">';
        
        foreach ($faqs as $faq) {
            $faq_html .= '<div class="faq-schema-item">';
            $faq_html .= '<div class="faq-question">';
            $faq_html .= '<span class="faq-icon">❓</span>';
            $faq_html .= '<span class="faq-text">' . esc_html($faq['question']) . '</span>';
            $faq_html .= '<span class="faq-toggle">▼</span>';
            $faq_html .= '</div>';
            $faq_html .= '<div class="faq-answer">';
            $faq_html .= '<p>' . wp_kses_post(wpautop($faq['answer'])) . '</p>';
            $faq_html .= '</div>';
            $faq_html .= '</div>';
        }
        
        $faq_html .= '</div>';
        $faq_html .= '</div>';
        
        // Add to content based on position
        if ($position === 'top') {
            return $faq_html . $content;
        } else {
            return $content . $faq_html;
        }
    }
    
    // Add Schema.org JSON-LD to Head
    public function add_faq_schema_markup() {
        // Only on single posts
        if (!is_singular('post')) {
            return;
        }
        
        global $post;
        $faqs = get_post_meta($post->ID, '_faq_items', true);
        
        // No FAQs? Don't output schema
        if (empty($faqs) || !is_array($faqs)) {
            return;
        }
        
        // Build Schema.org FAQPage structure
        $schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array()
        );
        
        foreach ($faqs as $faq) {
            $schema['mainEntity'][] = array(
                '@type'          => 'Question',
                // Strip HTML tags and decode entities for plain text in schema
                'name'           => wp_strip_all_tags($faq['question']),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    // Strip HTML tags and decode entities for plain text in schema
                    'text'  => wp_strip_all_tags($faq['answer'])
                )
            );
        }
        
        // Output JSON-LD with proper security flags
        // JSON_HEX_* flags convert potentially dangerous characters to Unicode escapes
        // Note: NOT using JSON_UNESCAPED_UNICODE as it bypasses escaping (WordPress requirement)
        echo '<script type="application/ld+json">';
        echo wp_json_encode($schema, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        echo '</script>' . "\n";
    }
    
    // Frontend Styles
    public function enqueue_frontend_styles() {
        // Only on single posts
        if (!is_singular('post')) {
            return;
        }
        
        global $post;
        $faqs = get_post_meta($post->ID, '_faq_items', true);
        
        // Only load if there are FAQs
        if (empty($faqs)) {
            return;
        }
        
        // Add inline CSS
        wp_add_inline_style('wp-block-library', '
            /* FAQ Schema Section */
            .faq-schema-section {
                background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
                padding: 40px 35px;
                border-radius: 16px;
                margin: 40px 0;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
                border: 2px solid #e5e7eb;
            }
            
            /* Title */
            .faq-schema-title {
                font-size: 2rem;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 30px 0;
                padding-bottom: 15px;
                border-bottom: 3px solid #667eea;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* FAQ List */
            .faq-schema-list {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }
            
            /* Individual FAQ Item */
            .faq-schema-item {
                background: white;
                border: 2px solid transparent;
                border-radius: 12px;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }
            
            .faq-schema-item:hover {
                border-color: #667eea;
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
                transform: translateY(-2px);
            }
            
            .faq-schema-item.active {
                border-color: #667eea;
                box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
            }
            
            /* Question */
            .faq-question {
                padding: 20px 24px;
                background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
                font-weight: 600;
                color: #1f2937;
                font-size: 1.1rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 14px;
                user-select: none;
                transition: all 0.2s;
                position: relative;
            }
            
            .faq-question:hover {
                background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            }
            
            .faq-schema-item.active .faq-question {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .faq-icon {
                font-size: 1.5rem;
                flex-shrink: 0;
                transition: transform 0.3s;
            }
            
            .faq-schema-item.active .faq-icon {
                transform: scale(1.1);
            }
            
            .faq-toggle {
                margin-left: auto;
                font-size: 1rem;
                color: #667eea;
                transition: transform 0.3s, color 0.2s;
                flex-shrink: 0;
                font-weight: 700;
            }
            
            .faq-schema-item.active .faq-toggle {
                transform: rotate(180deg);
                color: white;
            }
            
            /* Answer */
            .faq-answer {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s;
                background: white;
            }
            
            .faq-schema-item.active .faq-answer {
                max-height: 800px;
                padding: 24px;
                border-top: 2px solid #e5e7eb;
            }
            
            .faq-answer p {
                margin: 0 0 12px 0;
                color: #4b5563;
                line-height: 1.8;
                font-size: 1rem;
            }
            
            .faq-answer p:last-child {
                margin-bottom: 0;
            }
            
            /* Number Badge */
            .faq-question::before {
                content: counter(faq-counter);
                counter-increment: faq-counter;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                background: #667eea;
                color: white;
                border-radius: 50%;
                font-size: 0.875rem;
                font-weight: 700;
                flex-shrink: 0;
                transition: all 0.2s;
            }
            
            .faq-schema-item.active .faq-question::before {
                background: white;
                color: #667eea;
                transform: scale(1.1);
            }
            
            .faq-schema-list {
                counter-reset: faq-counter;
            }
            
            /* Mobile Responsive */
            @media (max-width: 768px) {
                .faq-schema-section {
                    padding: 25px 20px;
                    margin: 30px 0;
                }
                
                .faq-schema-title {
                    font-size: 1.5rem;
                    margin-bottom: 25px;
                }
                
                .faq-question {
                    padding: 16px 18px;
                    font-size: 1rem;
                    gap: 10px;
                }
                
                .faq-icon {
                    font-size: 1.25rem;
                }
                
                .faq-question::before {
                    width: 28px;
                    height: 28px;
                    font-size: 0.75rem;
                }
                
                .faq-schema-item.active .faq-answer {
                    padding: 18px;
                }
                
                .faq-answer p {
                    font-size: 0.95rem;
                }
            }
        ');
        
        // Add inline JavaScript for accordion
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Toggle FAQ on click
                $(".faq-question").on("click", function(e) {
                    e.preventDefault();
                    var item = $(this).closest(".faq-schema-item");
                    item.toggleClass("active");
                });
                
                // Open first FAQ by default
                $(".faq-schema-item:first").addClass("active");
            });
        ');
    }
    
    // Admin Styles
    public function admin_styles() {
        $screen = get_current_screen();
        if ($screen->post_type !== 'post' || !current_user_can('edit_posts')) {
            return;
        }
        
        wp_enqueue_style(
            'eneacodes-faq-schema-admin',
            plugins_url('admin-style.css', __FILE__),
            array(),
            '1.0.0'
        );
    }
    
    // Admin Scripts
    public function admin_scripts() {
        $screen = get_current_screen();
        if ($screen->post_type !== 'post' || !current_user_can('edit_posts')) {
            return;
        }
        
        // Enqueue jQuery UI Sortable for drag and drop functionality
        wp_enqueue_script('jquery-ui-sortable');
        
        // Enqueue our admin script
        wp_enqueue_script(
            'eneacodes-faq-schema-admin',
            plugins_url('admin-script.js', __FILE__),
            array('jquery', 'jquery-ui-sortable'),
            '1.0.0',
            true
        );
    }
}

// Initialize
new FAQ_Schema_Bros();
