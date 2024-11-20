<?php
/**
 * Template Name: UI Components Showcase
 * Template Post Type: page
 */

get_header();

// Get all saved components
$components = get_option('code_playground_components', array());

// Group components by type
$grouped_components = array(
    'shortcode' => array(),
    'component' => array(),
    'section' => array()
);

foreach ($components as $name => $data) {
    $grouped_components[$data['type']][$name] = $data;
}
?>

<div class="ui-showcase">
    <div class="showcase-header">
        <h1 class="showcase-title">UI Components Showcase</h1>
        <p class="showcase-description">A collection of all custom UI components and shortcodes</p>
    </div>

    <!-- Section Types -->
    <div class="type-selector">
        <button class="type-btn active" data-type="all">All</button>
        <button class="type-btn" data-type="shortcode">Shortcodes</button>
        <button class="type-btn" data-type="section">Sections</button>
        <button class="type-btn" data-type="component">Components</button>
    </div>

    <!-- Components Grid -->
    <div class="components-grid grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl mx-auto px-4">
        <?php foreach ($grouped_components as $type => $type_components): ?>
            <?php foreach ($type_components as $name => $data): ?>
                <div class="component-card flex flex-col bg-white rounded-lg shadow-md overflow-hidden max-h-[600px]" data-type="<?php echo esc_attr($type); ?>" data-name="<?php echo esc_attr($name); ?>">
                    <div class="component-header p-4 border-b">
                        <h3 class="text-lg font-semibold"><?php echo esc_html(ucwords(str_replace('-', ' ', $name))); ?></h3>
                        <div class="flex gap-2 mt-2">
                            <span class="type-badge rounded"><?php echo esc_html($type); ?></span>
                            <code class="shortcode-tag px-2 py-1 text-xs font-mono rounded">[<?php echo esc_html($name); ?>]</code>
                        </div>
                    </div>
                    <div class="component-preview flex-1 overflow-auto p-4" style="max-height: 400px;">
                        <?php if ($type === 'section'): ?>
                            <div class="tailwind-scope scale-50 origin-top">
                                <style><?php echo esc_html($data['css']); ?></style>
                                <?php echo wp_kses_post($data['html']); ?>
                                <script><?php echo esc_html($data['js']); ?></script>
                            </div>
                        <?php else: ?>
                            <div class="scale-75 origin-top">
                                <?php echo do_shortcode("[$name]"); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="component-info p-4 border-t bg-gray-50">
                        <div class="flex gap-2">
                            <button class="code-toggle px-4 py-2 text-sm bg-primary text-white rounded hover:bg-accent transition-colors" data-name="<?php echo esc_attr($name); ?>">View Code</button>
                            <button class="preview-toggle px-4 py-2 text-sm bg-secondary text-white rounded hover:bg-accent transition-colors" data-name="<?php echo esc_attr($name); ?>">Full Preview</button>
                        </div>
                        <div id="code-<?php echo esc_attr($name); ?>" class="code-panel" style="display: none;">
                            <div class="code-tabs">
                                <button class="tab-btn active" data-tab="html-<?php echo esc_attr($name); ?>">HTML</button>
                                <button class="tab-btn" data-tab="css-<?php echo esc_attr($name); ?>">CSS</button>
                                <button class="tab-btn" data-tab="js-<?php echo esc_attr($name); ?>">JS</button>
                            </div>
                            <div class="code-content">
                                <pre class="code-block active" id="html-<?php echo esc_attr($name); ?>"><?php echo esc_html($data['html']); ?></pre>
                                <pre class="code-block" id="css-<?php echo esc_attr($name); ?>"><?php echo esc_html($data['css']); ?></pre>
                                <pre class="code-block" id="js-<?php echo esc_attr($name); ?>"><?php echo esc_html($data['js']); ?></pre>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal for full preview -->
<div id="preview-modal" class="fixed inset-0 z-[9999] hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-6xl max-h-[90vh] bg-white rounded-lg shadow-xl overflow-auto pointer-events-auto">
            <div class="sticky top-0 flex justify-end p-4 bg-white border-b z-10">
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modal-content" class="p-6"></div>
        </div>
    </div>
</div>

<style>
/* Add custom styles */
.component-preview {
    position: relative;
    background: repeating-conic-gradient(#f0f0f0 0% 25%, #ffffff 0% 50%) 50% / 20px 20px;
}

.scale-50 {
    transform: scale(0.5);
}

.scale-75 {
    transform: scale(0.75);
}

.origin-top {
    transform-origin: top center;
}

#preview-modal {
    backdrop-filter: blur(5px);
    display: none; /* Ensure modal is hidden by default */
    align-items: center;
    justify-content: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
}

/* Ensure modal is always on top */
#preview-modal,
#preview-modal * {
    isolation: isolate;
}

/* Fix modal content positioning */
#modal-content {
    position: relative;
    z-index: 1;
    max-height: 80vh; /* Set maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
}

/* Reset scale in modal */
#modal-content .scale-50,
#modal-content .scale-75 {
    transform: none;
}
</style>

<script>
function closeModal() {
    const modal = document.getElementById('preview-modal');
    modal.style.display = 'none';
}

jQuery(document).ready(function($) {
    // Add type filtering functionality
    $('.type-btn').on('click', function() {
        // Update active button
        $('.type-btn').removeClass('active');
        $(this).addClass('active');

        // Filter components
        const type = $(this).data('type');
        $('.component-card').each(function() {
            if (type === 'all' || $(this).data('type') === type) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Replace onclick handlers with event listeners
    $(document).on('click', '.code-toggle', function() {
        const name = $(this).data('name');
        toggleCode(name);
    });

    $(document).on('click', '.preview-toggle', function() {
        const name = $(this).data('name');
        const componentPreview = $(`.component-card[data-name="${name}"] .component-preview`).html();
        $('#modal-content').html(componentPreview);
        $('#preview-modal').css('display', 'flex');
    });

    // Close modal when clicking overlay or close button
    $('#preview-modal .bg-black, #preview-modal button').on('click', function() {
        closeModal();
    });

    // Stop propagation on modal content click
    $('#preview-modal .max-w-6xl').on('click', function(e) {
        e.stopPropagation();
    });

    // Existing toggle and tab functionality
    function toggleCode(name) {
        const codePanel = document.getElementById(`code-${name}`);
        codePanel.style.display = codePanel.style.display === 'none' ? 'block' : 'none';
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tab-btn')) {
            const tabId = e.target.getAttribute('data-tab');
            const parent = e.target.closest('.code-panel');
            
            parent.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            
            parent.querySelectorAll('.code-block').forEach(block => block.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
        }
    });

    // Modal functionality
    function openModal(name) {
        const modal = document.getElementById('preview-modal');
        const modalContent = document.getElementById('modal-content');
        const component = document.querySelector(`.component-card[data-name="${name}"] .component-preview`).innerHTML;
        modalContent.innerHTML = component;
        modal.style.display = 'flex';
    }
});
</script>

<?php get_footer(); ?>
