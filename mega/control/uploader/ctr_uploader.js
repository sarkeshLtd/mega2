function ctr_uploader_onchange(obj,uploadUrl,port,name){
	$('.txtFileName' + name).val($(obj).val());
    var files = obj.files;
    
    // Create a new FormData object.
    var formData = new FormData();
    var file = files[0];
    
    // Add the file to the request.
    formData.append('uploads', file, file.name);
    formData.append('port', port);
     $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'html',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(data, textStatus, jqXHR){
				data = data.replace(/_a_n_d_/g,"&");
				var fileID = $(data).find('fileID').html();
				$('#id_ctr_uploader_' + name).val(fileID);
				var modal = $(data).find('msg').html();
				if(modal != '0'){
					SysShowModal(modal.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&'),'');
				}
				$('.btn_remove_file_' + name).removeAttr('disabled');
				$('.ctr_uploader_' + name).val('');
				$('.btn_select_file_' + name).attr('disabled','disabled');				
            }
    });
}

/*
 * function for remove file
 * @param string removeUrl,use for send remove file request
 * @param string $fileID ,id of file
 */
function control_remove_file(removeUrl,name){
	if($('#id_ctr_uploader_' + name).val() != ''){
		//send remove req
		$('.txtFileName' + name).val('');
		$('.btn_remove_file_' + name).attr('disabled','disabled');
		$('.ctr_uploader_' + name).val('');
		
		// Create a new FormData object.
		var formData = new FormData();
		// Add the file to the request.
		formData.append('sid', $('#id_ctr_uploader_' + name).val());
		 $.ajax({
				url: removeUrl,
				type: 'POST',
				data: formData,
				cache: false,
				dataType: 'html',
				processData: false, // Don't process the files
				contentType: false, // Set content type to false as jQuery will tell the server its a query string request
				success: function(data, textStatus, jqXHR){
					$('.btn_select_file_' + name).removeAttr('disabled');
				}
		});
	}
}
