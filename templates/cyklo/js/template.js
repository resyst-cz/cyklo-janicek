/**
 * @package     Joomla.Site
 * @subpackage  Templates.cyklo
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 */

(function ($)
{
    // rozsiri contains selector a udela non-case sensitive verzi
    $.extend($.expr[":"], {
        "containsIN": function (elem, i, match, array) {
            return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
        }
    });

    $(document).ready(function ()
    {
        $('*[rel=tooltip], *[data-toggle=tooltip]').tooltip();
        $('.nav.nav-tabs li:first-child a').each(function () {
            $(this).trigger('click');
        });

        // Turn radios into btn-group
        $('.radio.btn-group label').addClass('btn');
        $(".btn-group label:not(.active)").click(function ()
        {
            var label = $(this);
            var input = $('#' + label.attr('for'));

            if (!input.prop('checked')) {
                label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
                if (input.val() == '') {
                    label.addClass('active btn-primary');
                } else if (input.val() == 0) {
                    label.addClass('active btn-danger');
                } else {
                    label.addClass('active btn-success');
                }
                input.prop('checked', true);
            }
        });
        $(".btn-group input[checked=checked]").each(function ()
        {
            if ($(this).val() == '') {
                $("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
            } else if ($(this).val() == 0) {
                $("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
            } else {
                $("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
            }
        });
        /*
         *  Simple image gallery. Uses default settings
         */
        $('.fancybox').fancybox();

        /*
         *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
         */
        $('.fancybox-thumbs').fancybox({
            prevEffect: 'none',
            nextEffect: 'none',
            closeBtn: true,
            arrows: true,
            nextClick: true,
            helpers: {
                thumbs: {
                    width: 50,
                    height: 50
                }
            }
        });

        /*
         * Filtr pro kategorii kol
         */
        $('.filtr-tags .kategorie_tag').on('click', function () {
            var kategorie = $(this).attr('data-filtr_kategorie_id');
            $('.catmain_div .product-card').each(function () {
                if (kategorie == '-1') {
                    $(this).show(400);
                } else if ($(this).attr('data-kategorie_id') != kategorie) {
                    $(this).hide(400);
                } else {
                    $(this).show(400);
                }
            });
        });

        /**
         * Vyhledavani pro kategorii kol
         */
        $('#produkty-search-btn').on('click', function () {
            var search_string = $('#produkty-search').val();
            if (search_string == '') {
                $(".catmain_div .product-card").show(400);
            } else {
                $(".catmain_div .product-card:not(:containsIN('" + search_string + "'))").hide(400);
                $(".catmain_div .product-card:containsIN('" + search_string + "')").show(400);
            }
        });

        /**
         * kliknuti na tlacitko 'chci dalsi informace' v bazaru
         */
        $('.btn-email.modal').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id'),
                    produkt = $(this).closest(".product-card[data-id='" + id + "']"),
                    nazev = produkt.find('h3').text(),
                    cena = produkt.find('.cena:not(.puvodni) > span').text();
            $('.bazar-form .predmet > input').val(nazev + ' za ' + cena);
        });

        /**
         * zobrazeni popisu u znacek
         */
        $(document).on({
            mouseenter: function () {
                $(this).find('.znacka-popis').slideDown(200);
            },
            mouseleave: function () {
                $(this).find('.znacka-popis').slideUp(200);
            }
        }, '.znacka-wrapper');

    });
})(jQuery);