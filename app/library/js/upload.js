
// Actually confirm support
if (supportAjaxUploadWithProgress()) {
    // Ajax uploads are supported!
    // Change the support message and enable the upload button
    //var notice = document.getElementById('support-notice');
    var uploadBtn = $('.upload-button').removeAttr('disabled');

    initFullFormAjaxUpload();

    // Init the single-field file upload
    initFileOnlyAjaxUpload();
}


// Function that will allow us to know if Ajax uploads are supported
function supportAjaxUploadWithProgress () {
    return supportFileAPI() && supportAjaxUploadProgressEvents() && supportFormData();

    // Is the File API supported?
    function supportFileAPI() {
        var fi = document.createElement('INPUT');
        fi.type = 'file';
        return 'files' in fi;
    };

    // Are progress events supported?
    function supportAjaxUploadProgressEvents() {
        var xhr = new XMLHttpRequest();
        return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
    };

    // Is FormData supported?
    function supportFormData() {
        return !! window.FormData;
    }
}

function initFullFormAjaxUpload() {
    $('.upload-form').submit(function(e) {
        e.preventDefault();
        // FormData receives the whole form
        var formData = new FormData(this);

        // We send the data where the form wanted
        var action = this.getAttribute('action');

        // Code common to both variants
        sendXHRequest(formData, action);

        // Avoid normal form submission
        return false;
    });
}


function initFileOnlyAjaxUpload() {
    $('.upload-button').click(function (evt) {
        var formData = new FormData();
        // Since this is the file only, we send it to a specific location
        var action = 'upload.php?token='+token+'&ts='+new Date().getTime();

        // FormData only has the file
        var fileInput = document.getElementById('file-id');
        var file = fileInput.files[0];
        formData.append('our-file', file);

        // Code common to both variants
        sendXHRequest(formData, action);
        return false;
    });
}

// Once the FormData instance is ready and we know
// where to send the data, the code is the same
// for both variants of this technique
function sendXHRequest(formData, uri) {
    // Get an XMLHttpRequest instance
    var xhr = new XMLHttpRequest();

    // Set up events
    xhr.upload.addEventListener('loadstart', onloadstartHandler, false);
    xhr.upload.addEventListener('progress', onprogressHandler, false);
    xhr.upload.addEventListener('load', onloadHandler, false);
    xhr.addEventListener('readystatechange', onreadystatechangeHandler, false);

    // Set up request
    xhr.open('POST', uri+'&ts='+new Date().getTime(), true);

    // Fire!
    xhr.send(formData);

    return false;

}

// Handle the start of the transmission
function onloadstartHandler(evt) {
    upload_started();
}

// Handle the end of the transmission
function onloadHandler(evt) {
    //var div = document.getElementById('upload-status');
    //upload_finished(evt);
}

// Handle the progress
function onprogressHandler(evt) {
    var percent = evt.loaded/evt.total*100;
    upload_progress(percent);
}



// Handle the response froms the server
function onreadystatechangeHandler(evt) {
    var status = null;

    try {
        status = evt.target.status;
    }
    catch(e) {
        return;
    }
    if (status == '200' && evt.target.responseText) {
        upload_finished(evt);
        //var result = document.getElementById('result');
        //result.innerHTML = '<p>The server saw it as:</p><pre>' + evt.target.responseText + '</pre>';
    }
    return false;
}

