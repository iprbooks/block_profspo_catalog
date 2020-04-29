var type = 'book';
$(document).ready(function () {
    // init
    send_request_profspo(type);

    $('.profspo-form-control').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("profspo-filter-apply").click();
        }
    });

    $('.profspo-nav-link').click(function (e) {
        $(".profspo-nav-link").removeClass("active");
        $(this).addClass("active");
        $(".profspo-tab-pane").removeClass("active");
        $('#profspo-' + $(this).data("type")).addClass("active");
    })
});

// tab
$(".profspo-tab").click(function () {
    type = $(this).data("type");
    send_request_profspo(type);
});

// filter
$("#profspo-filter-apply").click(function () {
    send_request_profspo(type);
});

// clear filter
$("#profspo-filter-clear").click(function () {
    $(".profspo-filter").val("");
    send_request_profspo(type);
});


function send_request_profspo(type, page = 0) {
    var filter = $(".profspo-filter")
        .map(function () {
            return this.id + "=" + $(this).val();
        })
        .get()
        .join('&');

    $.ajax({
        url: M.cfg.wwwroot + "/blocks/profspo_catalog/ajax.php?action=getlist&type=" + type + "&page=" + page + "&" + encodeURI(filter)
    }).done(function (data) {

        // hide read button
        $("#profspo-item-detail-read").hide();

        // set data
        $("#profspo-items-list").scrollTop(0);
        $("#profspo-items-list").html(data.html);

        // set details click listener
        $(".profspo-item").click(function () {
            set_details_profspo($(this).data("id"));
        });

        // pagination
        $(".profspo-page").click(function () {
            send_request_profspo(type, $(this).data('page'));
        });

        // init detail view
        set_details_profspo($(".profspo-item").data("id"));
    });
}

function set_details_profspo(id) {
    this.clear_details_profspo();
    $("#profspo-item-detail-image").html($("#profspo-item-image-" + id).html());
    $("#profspo-item-detail-title").html($("#profspo-item-title-" + id).html());
    $("#profspo-item-detail-pubhouse").html($("#profspo-item-pubhouse-" + id).html());
    $("#profspo-item-detail-authors").html($("#profspo-item-authors-" + id).html());
    $("#profspo-item-detail-pubyear").html($("#profspo-item-pubyear-" + id).html());
    $("#profspo-item-detail-description").html($("#profspo-item-description-" + id).html());
    $("#profspo-item-detail-isbn").html($("#profspo-item-isbn-" + id).html());
    $("#profspo-item-detail-pubtype").html($("#profspo-item-pubtype-" + id).html());

    var rb = $("#profspo-item-detail-read");
    rb.attr("href", $("#profspo-item-url-" + id).attr("href"));
    if ($("#profspo-item-url-" + id).attr("href")) {
        rb.show();
    }
}

function clear_details_profspo() {
    $("#profspo-item-detail-image").html('');
    $("#profspo-item-detail-title").html('');
    $("#profspo-item-detail-pubhouse").html('');
    $("#profspo-item-detail-authors").html('');
    $("#profspo-item-detail-pubyear").html('');
    $("#profspo-item-detail-description").html('');
    $("#profspo-item-detail-isbn").html('');
    $("#profspo-item-detail-pubtype").html('');
}

