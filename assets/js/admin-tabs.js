jQuery(document).ready(function($) {
    // Get stored tab from localStorage
    var activeTab = localStorage.getItem('wpeazyai_active_tab');
    
    // Hide all tab content initially
    $('.tab-content').hide();
    
    
    // If there's a stored tab, make it active, otherwise show first tab
    if (activeTab) {
        $('.wpeazyai-nav-tab[data-tab="' + activeTab + '"]').addClass('nav-tab-active');
        $('#'+activeTab).show();
    } else {
        // Set first tab as active by default
        $('.wpeazyai-nav-tab:first').addClass('nav-tab-active');
        $('#config').show();
    }

    // Handle tab clicks
    $('.wpeazyai-nav-tab').on('click', function(e) {
        e.preventDefault();
        
        // Get clicked tab's data-tab value
        var tabId = $(this).data('tab');
        
        // Remove active class from all tabs
        $('.wpeazyai-nav-tab').removeClass('nav-tab-active');
        $('.tab-content').hide();
        
        // Add active class to clicked tab and show content
        $(this).addClass('nav-tab-active');
        $('#' + tabId).show();
        
        // Store active tab in localStorage
        localStorage.setItem('wpeazyai_active_tab', tabId);
    });
});