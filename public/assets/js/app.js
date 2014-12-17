/*!
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This file should be included in all pages
 !**/

$(function() {
    "use strict";

    //Enable sidebar toggle
    $("[data-toggle='offcanvas']").click(function(e) {
        e.preventDefault();

        //If window is small enough, enable sidebar push menu
        if ($(window).width() <= 992) {
            $('.row-offcanvas').toggleClass('active');
            $('.left-side').removeClass("collapse-left");
            $(".right-side").removeClass("strech");
            $('.row-offcanvas').toggleClass("relative");
        } else {
            //Else, enable content streching
            $('.left-side').toggleClass("collapse-left");
            $(".right-side").toggleClass("strech");
        }
    });

    //Add hover support for touch devices
    $('.btn').bind('touchstart', function() {
        $(this).addClass('hover');
    }).bind('touchend', function() {
        $(this).removeClass('hover');
    });

    //Activate tooltips & popovers
    $("[data-toggle='tooltip']").tooltip();
    $("[data-toggle='popover']").popover();

    /*
     * Add collapse and remove events to boxes
     */
    $(document).on("click", "#collapse-box", function() {
        //Find the box parent
        var box = $(this).parents(".box").first();
        //Find the body and the footer
        var bf = box.find(".box-body, .box-footer");
        if (!box.hasClass("collapsed-box")) {
            box.addClass("collapsed-box");
            //Convert minus into plus
            $(this).removeClass("fa-minus").addClass("fa-plus");
            bf.slideUp();
        } else {
            box.removeClass("collapsed-box");
            //Convert plus into minus
            $(this).removeClass("fa-plus").addClass("fa-minus");
            bf.slideDown();
        }
    });

    $("[data-widget='remove']").click(function() {
        //Find the box parent
        var box = $(this).parents(".box").first();
        box.slideUp();
    });

    /* Sidebar tree view */
    $(".sidebar .treeview").tree();

    /*
     * Make sure that the sidebar is streched full height
     * ---------------------------------------------
     * We are gonna assign a min-height value every time the
     * wrapper gets resized and upon page load. We will use
     * Ben Alman's method for detecting the resize event.
     **/
    function _fix() {
        //Get window height and the wrapper height
        var height = $(window).height() - $("body > .header").height();
        $(".wrapper").css("min-height", height + "px");
        var content = $(".wrapper").height();
        //If the wrapper height is greater than the window
        if (content > height)
            //then set sidebar height to the wrapper
            $(".left-side, html, body").css("min-height", content + "px");
        else {
            //Otherwise, set the sidebar to the height of the window
            $(".left-side, html, body").css("min-height", height + "px");
        }
    }

    function _fix_sidebar() {
        //Make sure the body tag has the .fixed class
        if (!$("body").hasClass("fixed")) {
            return;
        }

        //Add slimscroll
        $(".sidebar").slimscroll({
            height: ($(window).height() - $(".header").height()) + "px",
            color: "rgba(0,0,0,0.2)"
        });
    }

    //Fire upon load
    _fix();

    //Fire when wrapper is resized
    $(".wrapper").resize(function() {
        _fix();
        _fix_sidebar();
    });

    //Fix the fixed layout sidebar scroll bug
    _fix_sidebar();


});

/*
 * SIDEBAR MENU
 * ------------
 * This is a custom plugin for the sidebar menu. It provides a tree view.
 *
 * Usage:
 * $(".sidebar).tree();
 *
 * Note: This plugin does not accept any options. Instead, it only requires a class
 *       added to the element that contains a sub-menu.
 *
 * When used with the sidebar, for example, it would look something like this:
 * <ul class='sidebar-menu'>
 *      <li class="treeview active">
 *          <a href="#>Menu</a>
 *          <ul class='treeview-menu'>
 *              <li class='active'><a href=#>Level 1</a></li>
 *          </ul>
 *      </li>
 * </ul>
 *
 * Add .active class to <li> elements if you want the menu to be open automatically
 * on page load. See above for an example.
 */
(function($) {
    "use strict";

    $.fn.tree = function() {

        return this.each(function() {
            var btn = $(this).children("a").first();
            var menu = $(this).children(".treeview-menu").first();
            var isActive = $(this).hasClass('active');

            //initialize already active menus
            if (isActive) {
                menu.show();
                btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
            }
            //Slide open or close the menu on link click
            btn.click(function(e) {
                e.preventDefault();
                if (isActive) {
                    //Slide up to close menu
                    menu.slideUp();
                    isActive = false;
                    btn.children(".fa-angle-down").first().removeClass("fa-angle-down").addClass("fa-angle-left");
                    btn.parent("li").removeClass("active");
                } else {
                    //Slide down to open menu
                    menu.slideDown();
                    isActive = true;
                    btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
                    btn.parent("li").addClass("active");
                }
            });

            /* Add margins to submenu elements to give it a tree look */
            menu.find("li > a").each(function() {
                var pad = parseInt($(this).css("margin-left")) + 10;

                $(this).css({"margin-left": pad + "px"});
            });

        });

    };


}(jQuery));

/* CENTER ELEMENTS */
(function($) {
    "use strict";
    jQuery.fn.center = function(parent) {
        if (parent) {
            parent = this.parent();
        } else {
            parent = window;
        }
        this.css({
            "position": "absolute",
            "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
            "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
        });
        return this;
    }
}(jQuery));

/*
 * Set the CSRF Token for Ajax too
 */
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    });
});

// Get some confirmations prepared. Generally speaking we can just class a element and
// then these will be used for confirmation

// Generic 'confirm' dialog code for forms.
// Make your submit button part of class confirmform, and viola
var currentForm;
$(document).on("click", ".confirmform", function(e){
   currentForm = $(this).closest("form");
   e.preventDefault();
   bootbox.confirm("Are you sure you want to continue?", function(confirmed) {
      if ( confirmed ) {
         currentForm.submit();
      }
   });
});

// Generic 'confirm' dialog code for links.
// Make your link button part of class confirmlink, and viola
$(document).on("click", "a.confirmlink", function(event){
   event.preventDefault()
   var url = $(this).attr("href");
   bootbox.confirm("Are you sure you want to continue?", function(confirmed) {
      if ( confirmed ) {
         window.location = url;
      }
   });
});

// Init datatables on, tables
(function($) {
    $("[id^=datatable]").dataTable({ paging:false, order:[] });
}(jQuery));

// Bind the search ajax to the search form and prepare the search
// logic.
// search_location comes from masterLayout.blade.php as a variable
function performSearch(q) {
    var request;
    if (q.length >= 3) {

        // abort any pending request
        if (request) {
            request.abort();
        }
        // Show the results box and a loader
        $("section#main-content")
            .html("<i class='fa fa-cog fa-spin'></i> Searching...");

        // fire off the request to /form.php
        request = $.ajax({
            url: search_location,
            type: "GET",
            data: {'q' : q}
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            $("section#main-content").html(response);

            // Init datatables if required
            if ($.fn.dataTable.isDataTable('table#datatable')) {
                $('table#datatable').DataTable();
            }
            else {
                table = $('table#datatable').DataTable( {
                    paging: false
                });
            }
        });

        // callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // log the error to the console
            console.error(
                "The following error occured: " + textStatus, errorThrown
            );
        });

    }
}

// Prevent the search form from being submitted
$("form#sidebar-form").submit(function(e) {
    e.preventDefault();
});

// Listen for events on the input
var timer;
$("input#search-field").keyup(function(e) {

    // Ignore keycodes that are not characters
    var c= String.fromCharCode(e.keyCode);
    var isWordCharacter = c.match(/\w/);
    if(!isWordCharacter) return;

    clearTimeout(timer);
    var ms = 500; // milliseconds

    timer = setTimeout(function() {
        var q = $("input#search-field").val();
        performSearch(q);
    }, ms);

});
