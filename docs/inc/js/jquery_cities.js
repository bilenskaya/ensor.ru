$(document).ready(function () {
    $('#country_id').change(function () {
        var country_id = $(this).val();
        if (country_id == '0') {
            $('#city_id').html('');
            $('#city_id').attr('disabled', true);
            return(false);
        }
        $('#city_id').html('<option>загрузка...</option>');
        $('#city_id').attr('disabled', true);
        var url = '/reg_select.php';
        $.get(
            url,
            "country_id=" + country_id,
            function (resulter) {
                if (resulter.type == 'error') {
                    alert('error');
                    return(false);
                }
                else {
                    var options = '';
                    $(resulter.cities).each(function() {
                        options += '<option value="' + $(this).attr('id') + '">' + $(this).attr('title') + '</option>';
                    });
                    $('#city_id').html(options);
                    $('#city_id').attr('disabled', false);
                }
               
            },
            "json"
        );
    });
});