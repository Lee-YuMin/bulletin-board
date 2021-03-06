function showDialog(title, message, url, callback) {
    let msg = {
        title: title,
        message: message,
        confirmCallback: () => {
          callback ? callback : '';

          if(url && url != '')
              window.location.hash = `#/${url}`;
        }
      }

    document.dispatchEvent(new CustomEvent('open-dialog', {detail : msg}));
};

function showError(e, url) {
    if(!e.detail.request.xhr.response.message)
        return;
    
    showDialog('Error', e.detail.request.xhr.response.message, url);
}