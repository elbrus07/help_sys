$(document).ready(function () {
    //Клик по кнопке добавления статьи
    $('#AddDataForm input[type=submit]').click(function () {
        let formData = $('#AddDataForm').serialize();
        $.post('../API/api.php', formData, function () {
            location.reload();
        });
    });

    //Клик по кнопке редактирования/удаления статьи
    $('#EditDataForm input[type=submit]').click(function (event) {
        event.preventDefault();
        let item = $(event.currentTarget);
        let hiddenAction = $('#EditDataForm input[type=hidden][name="action"]');
        if(item.attr('value') === 'Редактировать') {
            hiddenAction.attr('value', 'edit');
        }
        else
            hiddenAction.attr('value','remove');
        let formData = $('#EditDataForm').serialize();
        $.post('../API/api.php', formData, function () {
            location.reload();
        });
    });

    //Клик по кнопке редактирования/удаления HTML ребенка
    $('#HTMLChildrenForm input[type=submit]').click(function (event) {
        event.preventDefault();
        let item = $(event.currentTarget);
        let hiddenAction = $('#HTMLChildrenForm input[type=hidden][name="action"]');
        if(item.attr('value') === 'Добавить')
            hiddenAction.attr('value','add');
        else
            hiddenAction.attr('value','remove');
        let formData = $('#HTMLChildrenForm').serialize();
        $.post('../API/api.php', formData, function () {
            location.reload();
        });
    });

    //Изменение выбранного значения в <select>
    let ep = $('#editPath');
    ep.change(function () {
        $('#hidden_ep').val(ep.val());
        //Получение данных о статье, которая была выбрана в select
        $.post('../API/api.php', {'mode': 'article', 'action':'get', 'path': ep.val()}, function (result) {
            result = JSON.parse(result);
            //$('#edit_content').val(result.Content);
            tinymce.get('edit_content').setContent(result.Content);
            $('#edit_caption').val(result.Caption);
        });

        //Список HTML элементов, связанных со статьей
        $.post('../API/api.php', {'mode': 'html_children', 'action': 'get', 'path': ep.val()}, function (result) {
            let htmlChList = $('#HTMLChildrenList');
            //Очищаем старый список детей
            htmlChList.empty();
            /**
             * @typedef {{element_id: string}} child
             */
            /**
             * @type {{result: Object.<int, child>}}
             */
            result = JSON.parse(result);
            for (let i in result)
                htmlChList.append('<option>'+result[i].element_id+'</option>');

            htmlChList.trigger('change');
        });
    });

    //Изменения выбранного значения в <select> (HTML дети)
    $('#HTMLChildrenList').change(function () {
        $('#HTMLChildrenForm input[name="HTMLelId"]').attr('value',$('#HTMLChildrenList').val());
    });

});
