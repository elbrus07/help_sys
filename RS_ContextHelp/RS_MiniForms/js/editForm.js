function sendFormOnServer(selector,url) {
    let formData = $(selector).serialize();
    return $.ajax({
       method: 'POST',
       url: url,
       data: formData
    });
}