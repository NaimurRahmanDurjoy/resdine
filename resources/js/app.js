import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import 'select2';
import $ from 'jquery';

// Alpine Setup
window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();

// jQuery global
window.$ = $;
window.jQuery = $;

// Modern DOM ready
document.addEventListener("DOMContentLoaded", () => {

    // Helper: Reinitialize Alpine inside dynamic content
    function reinitAlpine() {
        const wrapper = document.getElementById('content-wrapper');
        if (wrapper) {
            Alpine.initTree(wrapper);
        }
    }

    // ------------------------------------
    // AJAX Link Handler (SPA + Modal)
    // ------------------------------------
    $(document).on('click', 'a[data-ajax-link]', function (e) {
        e.preventDefault();

        const url = $(this).attr('href');
        const isModal = $(this).data('modal') === true;

        if (isModal) {
            // Load into modal
            $.get(url + '?modal=true', function (html) {
                $('#global-modal .modal-content').html(html);
                $('#global-modal').removeClass('hidden').addClass('flex');
                reinitAlpine();
            });
        } else {
            // SPA content
            $('#ajax-loader').removeClass('hidden');

            $('#content-wrapper').load(url + ' #content-wrapper > *', function () {
                window.history.pushState({}, '', url);
                $('#ajax-loader').addClass('hidden');
                reinitAlpine();
            });
        }
    });

    // ------------------------------------
    // Close Modal
    // ------------------------------------
    $(document).on('click', '[data-close-modal]', function () {
        $('#global-modal').removeClass('flex').addClass('hidden');
    });

    // ------------------------------------
    // AJAX Form Submission
    // ------------------------------------
    $(document).on('submit', 'form[data-ajax-form]', function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr('action');
        const method = form.attr('method') ?? 'POST';
        const data = new FormData(this);

        $('#ajax-loader').removeClass('hidden');

        $.ajax({
            url: url,
            method: method,
            data: data,
            contentType: false,
            processData: false,
            success: function () {
                $('#ajax-loader').addClass('hidden');

                // Close modal after submit
                if (form.closest('#global-modal').length) {
                    $('#global-modal').removeClass('flex').addClass('hidden');
                }

                // Reload content
                const baseRoute = $('#content-wrapper').data('base-route');
                $('#content-wrapper').load(baseRoute + ' #content-wrapper > *', function () {
                    reinitAlpine();
                });
            },
            error: function (err) {
                $('#ajax-loader').addClass('hidden');
                console.error(err);
                alert('Error submitting form');
            }
        });
    });

    // ------------------------------------
    // Delete via AJAX
    // ------------------------------------
    $(document).on('click', '[data-ajax-delete]', function (e) {
        e.preventDefault();

        if (!confirm('Are you sure to want delete?')) return;

        const url = $(this).attr('href');
        $('#ajax-loader').removeClass('hidden');

        $.ajax({
            url: url,
            method: 'POST',
            data: { 
                _method: 'DELETE', 
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            success: function () {
                $('#ajax-loader').addClass('hidden');

                const baseRoute = $('#content-wrapper').data('base-route');
                $('#content-wrapper').load(baseRoute + ' #content-wrapper > *', function () {
                    reinitAlpine();
                });
            },
            error: function (err) {
                $('#ajax-loader').addClass('hidden');
                console.error(err);
                alert('Error deleting item');
            }
        });
    });

    // ------------------------------------
    // Browser Back/Forward Handling
    // ------------------------------------
    window.onpopstate = function () {
        $('#content-wrapper').load(location.href + ' #content-wrapper > *', function () {
            reinitAlpine();
        });
    };

});
