(function( $ ){
    $.fn.ajaxFile = function(element, success = false, func = null, validation = false, fileArrange = false, html = null, reloadIDs, closest = false, find = false, funcAction = null, reloading = false,  ) {
        $(this).on('submit', element, function(event) {
            event.preventDefault(); // impedir o submit normal
            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData(this);

            $(document).find("span.text-danger").remove();
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function(data)
                {
                    if(html != null){
                        if(closest){
                            form.closest(html).append(data);
                        } else if(find){
                            form.find(html).append(data);
                        } else{
                            html.append(data);
                        }
                    }

                    if(fileArrange){
                        var imgs = $(data).closest('.img-card');
                        var videos = $(data).closest('.video-card');
                        var pdfs = $(data).closest('.pdf-card');
                        var docs = $(data).closest('.doc-list');
                        var sheets = $(data).closest('.sheet-list');

                        $('#img-body').append(imgs);
                        $('#video-body').append(videos);
                        $('#pdf-body').append(pdfs);
                        $('#pdf-ul-list').append(docs);
                        $('#sheet-ul-list').append(sheets);

                        console.log(pdfs);

                        if(imgs.length > 0){
                            $('#imageCollapse').collapse();
                        }
                        if(videos.length > 0){
                            $('#videoCollapse').collapse();
                        }
                        if(pdfs.length > 0){
                            $('#pdfCollapse').collapse();
                        }
                        if(docs.length > 0){
                            $('#docCollapse').collapse();
                        }
                        if(sheets.length > 0){
                            $('#sheetCollapse').collapse();
                        }
                    }
        
                    if(!reloading){
                        // if(reloadIDs != null){
                        //     reloadIDs.forEach(function(element){
                        //         $(element).load(location.href + " "+  element + ">*", "");
                        //     });
                        // }
                    } else{
                        location.reload(); 
                    }
                    if(success){
                        Toast.fire({
                            icon: 'success',
                            title: 'Operação concluida com sucesso'
                        })
                    }

                    if(func != null){
                        func();
                    }

                },
                error: function (response){
                   
                    if(validation){
                        $erroStr = '';
                        $.each(response.responseJSON.errors, function(field_name, error){
                            $erroStr += " " + error + "\n";
                        });

                        Toast.fire({
                            icon: 'error',
                            title: $erroStr
                        })
                    }
                },
                cache: false,
                contentType: false,
                processData: false,

                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
                        myXhr.upload.addEventListener('progress', function() {
                            /* faz alguma coisa durante o progresso do upload */
                        }, false);
                    } // Custom XMLHttpRequest
                    return myXhr;
                }
            }).done(function(){
                
                if(funcAction != null){
                    funcAction(form);
                }
                

                if($('#loading').hasClass('d-none')){
                    $('#loading').removeClass('d-none');
                    setTimeout(function(){
                        $('#loading').addClass('d-none');
                    }, 1000);
                }
            });
        });
       return this;
    }; 
 }) ( jQuery );