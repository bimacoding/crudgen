<script>
    $(document).ready(function() {
        var count = 1;
        dynamic_field(count);
        $(document).on('click', '#add', function() {
            count++;
            dynamic_field(count);
        });

        $(document).on('click', '.remove', function() {
            count--;
            $(this).closest("tr").remove();
        });


    });
    $('#dynamic_form').on('submit', function(event) {
        event.preventDefault();
        console.log($(this).serialize());
        $.ajax({
            url: "{{ route('crud.insert') }}",
            method: 'post',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('#save').attr('disabled', 'disabled');
            },
            success: function(data) {
                if (data.error) {
                    $(".print-error-msg").show();
                    printErrorMsg(data.error);
                } else {
                    $(".print-success-msg").show();
                    printSuccessMsg(data.success);
                    dynamic_field(1);
                }
                $("#dynamic_form").trigger('reset');
                $('#save').attr('disabled', false);
            }
        })
    });

    function printErrorMsg(msg) {
        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").css('display', 'block');
        if (typeof msg == "object") {
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        } else {
            $(".print-error-msg").find("ul").append('<li>' + msg + '</li>');
        }
    }

    function printSuccessMsg(msg) {
        $(".print-success-msg").find("ul").html('');
        $(".print-success-msg").css('display', 'block');
        if (typeof msg == "object") {
            $.each(msg, function(key, value) {
                $(".print-success-msg").find("ul").append('<li>' + value + '</li>');
            });
        } else {
            $(".print-success-msg").find("ul").append('<li>' + msg + '</li>');
        }
    }

    function dynamic_field(number) {
        html = '<tr>'
        html +=
            '<td width="200" class="p-1"><input type="text" name="addmore[' + number +
            '][fieldNameForm]" id="fieldNameForm" class="form-control"></td>'
        html +=
            '<td width="200" class="p-1"><select name="addmore[' + number +
            '][dbTypeForm]" id="dbTypeForm" class="form-control"><option value="increments"> Increments </option><option value="integer"> Integer </option><option value="biginteger"> BigInteger </option><option value="string"> String </option><option value="timestamps"> Timestamps </option><option value="text"> Text </option><option value="longtext"> LongText </option><option value="mediumtext"> MediumText </option><option value="boolean"> Boolean </option><option value="float"> Float </option><option value="double"> Double </option></select></td>'
        html +=
            '<td width="200" class="p-1"><input type="text" name="addmore[' + number +
            '][validationsForm]" id="validationsForm" class="form-control"></td>'
        html +=
            '<td width="200" class="p-1"><select name="addmore[' + number +
            '][htmlTypeForm]" id="htmlTypeForm" class="form-control"><option value="number"> number </option><option value="text"> text </option><option value="textarea"> textarea </option><option value="password"> password </option><option value="email"> email </option><option value="date"> date </option></select></td>'
        html += '<td class="p-1"><center><input type="checkbox" name="addmore[' + number +
            '][primaryForm]" ></center></td>'
        html += '<td class="p-1"><center><input type="checkbox" name="addmore[' + number +
            '][isForeignForm]"></center></td>'
        html += '<td class="p-1"><center><input type="checkbox" name="addmore[' + number +
            '][searchableForm]"></center></td>'
        html += '<td class="p-1"><center><input type="checkbox" name="addmore[' + number +
            '][fillableForm]"></center></td>'
        html += '<td class="p-1"><center><input type="checkbox" name="addmore[' + number +
            '][inFormForm]"></center></td>'
        html += '<td class="p-1"><center><input type="checkbox" name="addmore[' + number +
            '][inIndexForm]"></center></td>'
        if (number > 1) {
            html +=
                '<td class="p-1"><button class="btn btn-link text-danger remove" name="remove" type="button"><i class="bx bx-trash-alt"></i></button></td></tr>'
            $('#addnewitem').append(html);
        } else {
            html +=
                '<td class="p-1"><button class="btn btn-link text-success add" name="add" id="add" type="button"><i class="bx bx-plus"></i></button></td></tr>'
            $('#addnewitem').html(html);
        }
    }
</script>