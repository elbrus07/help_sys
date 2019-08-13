$(document).ready(function () {
    //Клик по кнопке добавления статьи
    $('#AddDataForm input[type=submit]').click(function (event) {
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
            $('#edit_content').val(result.Content);
            $('#edit_caption').val(result.Caption);
        });

        //Список HTML элементов, связанных со статьей
        $.post('../API/api.php', {'mode': 'html_children', 'action': 'get', 'path': ep.val()}, function (result) {
            let htmlChList = $('#HTMLChildrenList');
            //Очищаем старый список детей
            htmlChList.empty();
            /**
             * @typedef {{element_id: string, uniqueClass: string, pathname: string}} child
             */
            /**
             * @type {{result: Object.<int, child>}}
             */
            result = JSON.parse(result);
            for (let i in result)
                htmlChList.append('<option value=\''+ JSON.stringify(result[i]) +'\'>'+result[i].element_id+'</option>');
            //Очистка значений в полях для ввода (или добавление значения первого ребенка в списке)
            let attr1 = '';
            let attr2 = '';
            let attr3 = '/';
            if(result.length > 0)
            {
                attr1 = result[0].element_id;
                attr2 = result[0].uniqueClass;
                attr3 = result[0].pathname;
            }
            $('#HTMLChildrenForm input[name="HTMLelId"]').attr('value',attr1);
            $('#HTMLChildrenForm input[name="uniqueClass"]').attr('value',attr2);
            $('#HTMLChildrenForm input[name="pathname"]').attr('value',attr3);
        });
    });

    //Изменения выбранного значения в <select> (HTML дети)
    let htmlChList = $('#HTMLChildrenList');
    htmlChList.change(function () {
        let arr = JSON.parse(htmlChList.val());
        $('#HTMLChildrenForm input[name="HTMLelId"]').attr('value',arr.element_id);
        $('#HTMLChildrenForm input[name="uniqueClass"]').attr('value',arr.uniqueClass);
        $('#HTMLChildrenForm input[name="pathname"]').attr('value',arr.pathname);
    });

});
