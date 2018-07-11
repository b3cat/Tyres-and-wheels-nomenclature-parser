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
    $('.whitelist-models').load('/tiresandwheels/whitelists/search/'+categoryId);
});
$(document).on('click', '.show-whitelist', function (e) {
    e.preventDefault();
    let categoryId = $(this).data('categoryid');
    $.get( '/tiresandwheels/whitelists/get/' + categoryId, function( data ) {
        console.log(data);
        $('.whitelist-body').html(data.whitelist);
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
    console.log(123);
});
$(document).on('submit', '.update-whitelist-form', function (e) {
    let form = $(this);
    e.preventDefault();
    console.log(form.closest('.update-result'));
    $.post('/tiresandwheels/whitelist/update', form.serialize());
    form.closest('.update-result').load('/tiresandwheels/whitelist/update', form.serializeArray());
});


console.log('123');