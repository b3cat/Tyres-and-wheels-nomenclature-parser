import $ from 'jquery';

window.$ = window.jQuery = $;

import 'jquery-ui/ui/widgets/autocomplete.js';

$("#q").autocomplete({
    source: "search/autocomplete",
    minLength: 3,
    select: function (event, ui) {
        $('#q').val(ui.item.value);
    }
});
