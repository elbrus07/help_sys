//Модуль, отвечающий за контекстную справку
$(document).ready(function () {
    //Получаем элементы с классом RS_Help
    let item = $('.RS_Help');

    item.mouseenter(function (event) {
        //Проверка на наличие открытых окон
        if(!$('*').is('.RS_FormClose')) {
            //Если открытых окон нет, то
            //При наведении на элемент класса RS_Help выводим
            //значок помощи
            let senderElement = $(event.currentTarget);
            $('<div>', {
                class: 'RS_InfoButton',
                css: {
                    top: senderElement.offset().top,
                    left: senderElement.offset().left
                },
                on: {
                    click: function () {
                        let id = senderElement.attr('id');
                        let scriptDir = getScriptDir();
                        //Получаем статью справки для нашего элемента
                        $.ajax({
                            //url: "/ReferenceSystem/API/api.php",
                            url: scriptDir + "/../../API/api.php",
                            method: 'POST',
                            data: {
                                'mode': 'html_children',
                                'action': 'getArticle',
                                'item_id': id
                            }
                        })
                            .done(function (result) {
                                //Готовим форму
                                let form = $('<div>', {
                                    id: 'RSForm',
                                    css: {
                                        position: 'absolute',
                                        top: senderElement.offset().top,
                                        left: senderElement.offset().left,
                                    }
                                });
                                //Получаем данные об авторизации
                                $.post(scriptDir + "/../../API/api.php",{'mode': 'another', 'action': 'isAdmin'}, function (is_loggedIn) {
                                    /**
                                     * @param result массив со статьей справки
                                     * @param result.Id id статьи
                                     * @param result.Content содержимое статьи
                                     * @param result.Caption заголовок статьи
                                     */
                                    result = JSON.parse(result);
                                    let url;
                                    let data;
                                    //Если пользователь авторизован (т.е. является администратором справочной системы)
                                    if (is_loggedIn) {
                                        //Если у элемента есть статья справки,
                                        if (result.Id !== -1) {
                                            //то готовим форму для редактирования статьи / отвязки от статьи,
                                            url = scriptDir + '/../../ContextHelp/MiniForms/editForm.php';
                                            data = {
                                                'aid': result.Id,
                                                'aPath': result.Path,
                                                'aContent': result.Content,
                                                'aCaption': result.Caption,
                                                'html_id': id
                                            };
                                        }
                                        //иначе,
                                        else {
                                            //готовим форму для создания статьи / привязки к статье
                                            url = scriptDir + '/../../ContextHelp/MiniForms/addForm.php';
                                            data = {
                                                'html_id': id
                                            };
                                        }
                                    }
                                    //Если не авторизован (т.е. обычный пользователь)
                                    else {
                                        if (result.Id !== -1) {
                                            //то готовим форму для редактирования статьи / отвязки от статьи,
                                            url = scriptDir + '/../../ContextHelp/MiniForms/showArticleForm.php';
                                            data = {
                                                'page_id': result.Id
                                            };
                                        } else {
                                            url = scriptDir + '/../../ContextHelp/MiniForms/error.php';
                                        }
                                    }
                                    //Загружаем данные в форму, предварительно добавив на нее кнопку "Закрыть"
                                    form.load(url, data, function () {
                                        form.prepend('<div class="RS_FormClose"></div>');
                                        form.appendTo($('html'));
                                    });
                                });
                            })
                            .fail(function () {

                            });
                    }
                }
            }).appendTo(senderElement);
        }
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


function getScriptDir() {
    let scripts = document.getElementsByTagName("script");
    for (let i in scripts)
    {
        let n = scripts[i].src.search('ReferenceSystem/ContextHelp/js/');
        if(n !== -1)
            return scripts[i].src.substr(0,n+30);
    }
    return false;
}