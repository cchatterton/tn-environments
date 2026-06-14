(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var tabLinks = document.querySelectorAll('.tab-link');
        var tabContents = document.querySelectorAll('.tab-content');
        var urlParams = new URLSearchParams(window.location.search);
        var currentTab = urlParams.get('tab') || 'tab-1';
        var message = document.getElementById('message');

        activateTab(currentTab);

        tabLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                var tabId = link.getAttribute('data-tab');
                var url = new URL(window.location);

                url.searchParams.set('tab', tabId);
                window.location.href = url.toString();
            });
        });

        if (message) {
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        }

        function activateTab(tabId) {
            tabLinks.forEach(function(link) {
                link.classList.toggle('current', link.dataset.tab === tabId);
            });

            tabContents.forEach(function(content) {
                content.classList.toggle('current', content.id === tabId);
            });
        }
    });
})();
