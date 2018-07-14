$(function () {
    $.ajaxSetup({
        headers: {
            'X-XSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });
});
$('form.js-ajax').on('submit', function (event) {
    event.preventDefault();

    let formData = $(this).serialize(); // form data as string
    let formAction = $(this).attr('action'); // form handler url
    let formMethod = '';
    if ($(this).find('[name="_method"]').length > 0) {
        formMethod = $(this).find('[name="_method"]').first().val();
    } else {
        formMethod = $(this).attr('method'); // GET, POST
    }

    $.ajax({
        type: formMethod,
        url: formAction,
        data: formData,
        cache: false,

        beforeSend: function () {
            console.log(formData);
        },

        success: function (data) {
            console.log(data);
        },

        error: function () {

        }
    });

    // console.log(formData);

    return false; // prevent send form
});
$('.js-phone-you').mask('+0(000)000-00-00');
$('.js-categories-search').on('input', function (e) {
    let form = $(this).closest('form').serializeArray();
    $('.whitelist-editor-content').load('/tiresandwheels/whitelists/search', form);
});
$(document).on('click', '.js-show-models', function (e) {
    e.preventDefault();
    let categoryId = $(this).data('categoryid');
    $('.whitelist-models').load('/tiresandwheels/whitelists/search/' + categoryId);
});
$(document).on('click', '.show-whitelist', function (e) {
    e.preventDefault();
    let categoryId = $(this).data('categoryid');
    $.get('/tiresandwheels/whitelists/get/' + categoryId, function (data) {
        console.log(data);
        // $('.whitelist-body').html(data.whitelist);
        $('.whitelist-body').val(data.whitelist)
        $('.category-id-input').val(data.categoryId);
    }, 'json');
});
$(document).on('click', '.send-error', function (e) {
    alert('1');
    e.preventDefault();
    let productId = $(this).data('productid');
    $.get('/tiresandwheels/parsererror/' + productId);
});

$('.show-whitelists').click(function (elem) {
    elem.preventDefault();
    $('.whitelists-block').slideToggle();

});


$(document).on('submit', '.update-product-form', function (e) {
    let form = $(this);
    e.preventDefault();
    form.closest('.product-wrapper').load('/tiresandwheels/products/update', form.serializeArray());
});
$(document).on('click', '.parse-again', function (e) {
    e.preventDefault();
    let productId = $(this).data('productid');
    $(this).closest('.product-wrapper').load('/tiresandwheels/parseagain/' + productId);
});
$(document).on('submit', '.update-whitelist-form', function (e) {
    let form = $(this);
    e.preventDefault();
    $.post('/tiresandwheels/whitelist/update', form.serialize());

});

$(document).on('submit', '.update-reg-exp', function (e) {
    e.preventDefault();
    let form = $(e.target);

    $.post('/tiresandwheels/parser/regexp/update', form.serializeArray());
});
$(document).on('submit', '.test-parse', function (e) {
    e.preventDefault();
    let form = $(this);
    $.post('/tiresandwheels/parser/test', form.serializeArray(), (data) => {
        form.closest('.check-parser').find('.parsing-result').html(data);
    });
});
$(document).on('submit', '.make-a-pair', function (e) {
    e.preventDefault();
    let form = $(this);
    $.post('/tiresandwheels/parser/pairfields/make', form.serializeArray(), (data) => {
        $('.show-table').html(data);
    });
});

$(document).on('click', '.show-add-reg-exp-form', function (e) {
    e.preventDefault();
    $(this).closest('.row').find('.add-reg-exp-form').toggle();
});
$(document).on('submit', '.add-reg-exp', (e) => {
    e.preventDefault();
    let form = $(e.target);
    $.post('/tiresandwheels/parser/regexps/add', form.serializeArray(), (data) => {
        form.closest('.reg-exp-container').html(data);
    }, 'html');
});
$(document).on('click', '.delete-reg-exp', (e) => {
    e.preventDefault();
    let container = $(e.target).closest('.reg-exp-container');
    let regExpId = $(e.target).data('regexpid');
    $.ajax({
        url: '/tiresandwheels/parser/regexps/delete/' + regExpId,
        type: 'post',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
        },

        success: function (data) {
            container.html(data);
        }
    });

    // $.post('/tiresandwheels/parser/regexps/delete/' + regExpId, (data) => {
    //     container.html(data);
    // });
});


console.log('1234');