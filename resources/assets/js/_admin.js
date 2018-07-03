$(function() {
    $.ajaxSetup({
        headers: {
            'X-XSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });
});
$('form.js-ajax').on('submit', function(event) {
    event.preventDefault();

    let formData = $(this).serialize(); // form data as string
    let formAction = $(this).attr('action'); // form handler url
    let formMethod = '';
    if($(this).find('[name="_method"]').length > 0){
        formMethod = $(this).find('[name="_method"]').first().val();
    }else{
        formMethod = $(this).attr('method'); // GET, POST
    }

    $.ajax({
        type  : formMethod,
        url   : formAction,
        data  : formData,
        cache : false,

        beforeSend : function() {
            console.log(formData);
        },

        success : function(data) {
            console.log(data);
        },

        error : function() {

        }
    });

    // console.log(formData);

    return false; // prevent send form
});
$('.js-phone-you').mask('+0(000)000-00-00');
console.log('123');