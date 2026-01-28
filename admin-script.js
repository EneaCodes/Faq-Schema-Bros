jQuery(document).ready(function($) {
    var faqIndex = $('.faq-item-modern').length;
    
    // Initialize Sortable (Drag and Drop)
    $("#faq-list").sortable({
        handle: ".faq-drag-handle",
        placeholder: "faq-sortable-placeholder",
        cursor: "grabbing",
        tolerance: "pointer",
        opacity: 0.8,
        start: function(e, ui) {
            // Adjust placeholder height to match item being dragged
            ui.placeholder.height(ui.item.height());
            // Add a class to the item being dragged for CSS styling
            ui.item.addClass("faq-sorting");
        },
        stop: function(e, ui) {
            // Remove styling class
            ui.item.removeClass("faq-sorting");
            // Update indices and input names
            updateFaqNumbers();
        }
    });
    
    // Prevent inputs from initiating drag when trying to select text
    $("#faq-list input, #faq-list textarea").on('mousedown', function(e) {
        e.stopPropagation();
    });
    
    // Update FAQ title on question input
    $(document).on('input', '.faq-input', function() {
        var question = $(this).val();
        var title = question || 'New FAQ';
        $(this).closest('.faq-item-modern').find('.faq-item-title').text(title);
    });
    
    // Character counter for answers
    $(document).on('input', '.faq-textarea', function() {
        var current = $(this).val().length;
        $(this).siblings('.faq-char-count').find('.char-current').text(current);
        
        // Warning if over 500 chars
        if (current > 500) {
            $(this).siblings('.faq-char-count').css('color', '#dc3545');
        } else {
            $(this).siblings('.faq-char-count').css('color', '#9ca3af');
        }
    });
    
    // Initialize character counters
    $('.faq-textarea').each(function() {
        var current = $(this).val().length;
        $(this).siblings('.faq-char-count').find('.char-current').text(current);
    });
    
    // Add new FAQ (both buttons)
    $('#add-faq-btn, #add-faq-btn-top').on('click', function(e) {
        e.preventDefault();
        
        var newFaq = `
            <div class="faq-item-modern" data-index="${faqIndex}">
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
                    <div class="faq-item-number">#${faqIndex + 1}</div>
                    <div class="faq-item-title">New FAQ</div>
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
                               name="faq_items[${faqIndex}][question]" 
                               value="" 
                               placeholder="e.g., Is parking available at this beach?"
                               class="faq-input">
                    </div>
                    
                    <div class="faq-input-group">
                        <label class="faq-label">
                            <span class="label-icon">💬</span>
                            Answer
                        </label>
                        <textarea name="faq_items[${faqIndex}][answer]" 
                                  rows="3" 
                                  class="faq-textarea"
                                  placeholder="e.g., Yes, there is free parking available about 100 meters from the beach entrance."></textarea>
                        <div class="faq-char-count">
                            <span class="char-current">0</span> / <span class="char-max">500</span> characters
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#faq-list').append(newFaq);
        faqIndex++;
        updateFaqNumbers();
        
        // Scroll to new FAQ
        var newItem = $('.faq-item-modern').last();
        $('html, body').animate({
            scrollTop: newItem.offset().top - 100
        }, 300);
        
        // Focus on question input
        newItem.find('.faq-input').focus();
    });
    
    // Remove FAQ
    $(document).on('click', '.faq-delete-btn', function(e) {
        e.preventDefault();
        
        if ($('.faq-item-modern').length > 1) {
            if (confirm('Are you sure you want to delete this FAQ?')) {
                $(this).closest('.faq-item-modern').fadeOut(300, function() {
                    $(this).remove();
                    updateFaqNumbers();
                    updateFaqCount();
                });
            }
        } else {
            alert('You must have at least one FAQ item. Clear the content if you don\'t want to use FAQs.');
        }
    });
    
    // Update FAQ numbers and indices
    function updateFaqNumbers() {
        $('.faq-item-modern').each(function(index) {
            $(this).find('.faq-item-number').text('#' + (index + 1));
            $(this).attr('data-index', index);
            
            // Update input names
            $(this).find('input[name*="[question]"]').attr('name', 'faq_items[' + index + '][question]');
            $(this).find('textarea[name*="[answer]"]').attr('name', 'faq_items[' + index + '][answer]');
        });
    }
    
    // Update FAQ count in header
    function updateFaqCount() {
        var count = $('.faq-item-modern').length;
        $('.faq-stat-number').text(count);
    }
    
    // Copy question example to input on click
    $(document).on('click', '.faq-tip-tag', function(e) {
        e.preventDefault();
        // Use data-question attribute instead of text for security
        var question = $(this).data('question');
        
        // Find first empty FAQ or create new one
        var emptyFaq = $('.faq-input').filter(function() {
            return $(this).val() === '';
        }).first();
        
        if (emptyFaq.length) {
            emptyFaq.val(question).trigger('input');
            $('html, body').animate({
                scrollTop: emptyFaq.offset().top - 100
            }, 300);
            emptyFaq.focus();
        } else {
            // Add new FAQ with this question
            $('#add-faq-btn').click();
            setTimeout(function() {
                $('.faq-input').last().val(question).trigger('input').focus();
            }, 100);
        }
    });
});
