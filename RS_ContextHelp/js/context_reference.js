//Модуль, отвечающий за контекстную справку
$(document).ready(function () {
    //Получаем элементы с классом RS_Help
    let item = $('.RS_Help');

    item.mouseenter(function (event) {
        //При наведении на элемент класса RS_Help выводим
        //значок помощи
        let senderElement = $( event.currentTarget );
        $('<div>', {
            class: 'RS_InfoButton',
            css: {
                top: senderElement.offset().top,
                left: senderElement.offset().left
            },
            on: {
                click: function () {
                    //При клике на который получаем список классов элемента .RS_Help
                    let classes = senderElement.attr('class').split(' ');
                    //В списке элементов проверяем существование элемента,
                    //отвечающего за уникальность id на странице
                    let uniqueClass = '';
                    for(let i in classes)
                    {
                        let cl = classes[i].split('_');
                        if(cl.length === 2 && cl[1][0] === 'D')
                        {
                            uniqueClass = classes[i];
                        }
                    }
                    let id = senderElement.attr('id');
                    //Получаем статью справки для нашего элемента
                    $.ajax({
                        url: '/RS_ContextHelp/RS_MiniForms/miniFormsActions.php',
                        method: 'POST',
                        data: {
                            'mode': 'getArticle',
                            'item_id': id,
                            'uniqueClass': uniqueClass,
                            'pathname': $(location).attr('pathname')

                        }
                    })
                        .done(function (result) {
                            //Готовим форму
                            let form = $('<div>', {
                                css: {
                                    id: 'RSForm',
                                    position: 'absolute',
                                    top: senderElement.offset().top,
                                    left: senderElement.offset().left,
                                    border: '2px solid black',
                                    'border-radius': '15px',
                                    background: 'white',
                                    'box-shadow': '-8px 7px 16px 0px black'
                                }
                            });
                            result = JSON.parse(result);
                            let url;
                            let data;
                            //Если у элемента есть статья справки,
                            if (result.length > 0) {
                                //то готовим форму для редактирования статьи / отвязки от статьи,
                                result = result[0];
                                url = '/RS_ContextHelp/RS_MiniForms/editForm.php';
                                data = {
                                    'aid': result.Id,
                                    'aContent': result.Content,
                                    'aCaption': result.Caption,
                                    'html_id': id,
                                    'uniqueClass': uniqueClass
                                };
                            }
                            //иначе,
                            else {
                                //готовим форму для создания статьи / привязки к статье
                                url = '/RS_ContextHelp/RS_MiniForms/addForm.php';
                                data = {
                                    'html_id': id,
                                    'uniqueClass': uniqueClass
                                };
                            }
                            //Загружаем данные в форму, предварительно добавив на нее кнопку "Закрыть"
                            form.load(url,data, function () {
                                form.prepend('<div class="RS_FormClose"></div>');
                                form.appendTo($('html'));
                            });
                        })
                        .fail(function () {

                        });
                }
            }
        }).appendTo(senderElement);

    });

    //Закрыте контестной формы
    $(document).on('click', '.RS_FormClose', function (element) {
        $(element.currentTarget).parent().remove();
    });

    item.mouseleave(function () {
        //Удаляем значок вопроса
        $('.RS_InfoButton').remove();
    });
});