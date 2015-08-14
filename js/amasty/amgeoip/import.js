Event.observe(window, 'load', function(){
    window.amImportObj = new amImport();
});

var amImport = new Class.create();
var isDownloadStarted = false;
var prevStartUrl;
var prevProcessUrl;
var prevCommitUrl;

amImport.prototype = {
    initialize: function(){

    },
    error: function(error, processer){
        if (processer)
            $(processer.parentNode).remove();

    },
    tracePosition: function(position, processer){
        processer.each(function(d){
            d.setStyle({'width': position + '%'});
        });

        processer.each(function(d){
            d.down('span').innerHTML = position + '%';
        });
    },
    done: function(response){
        if (response.full_import_done == 1){
            location.reload();
        }
    },

    commit: function(commitUrl, processer){
        var _caller = this;

        var request = new Ajax.Request(
            commitUrl,
            {
                method: 'post',
                onSuccess: function(transport){
                    var response = eval('(' + transport.responseText + ')');

                    if (response.status == 'done'){
                        _caller.done(response)
                    } else if (response.error){
                        _caller.error(response.error, processer);
                    }
                }
            }
        );
    },
    process: function(processUrl, commitUrl, processer){
        var _caller = this;

        var request = new Ajax.Request(
            processUrl,
            {
                method: 'post',
                onSuccess: function(transport){
                    var response = eval('(' + transport.responseText + ')');

                    if (response.status == 'processing'){

                        if (response.type == 'block') {
                            _caller.tracePosition(response.position, processer);
                        }


                        if (response.position == 100){
                            _caller.commit(commitUrl, processer);
                        } else {
                            _caller.process(processUrl, commitUrl, processer);
                        }


                    } else if (response.error){
                        _caller.error(response.error, processer);
                    }
                }
            }
        );
    },

    startDownloading: function(input){
        var processer = $$('div.am_download');

        return processer;
    },

    runDownloading: function(startUrl, processUrl, commitUrl, startDownloadingUrl, input){
        var _caller = this;
        var processer = _caller.startDownloading(input);

        processer.each(function(d){
            d.setStyle({
              'width': '30%'
          });
        });

        processer.each(function(d){
            d.down('span').innerHTML = '0/2';
        });

        $$(".am_processer_container .end_downloading_completed").each(function(d){
            d.removeClassName('end_downloading_completed');
            d.addClassName('end_downloading_process');
        });

        $$(".am_processer_container .end_downloading_not_completed").each(function(d){
            d.removeClassName('end_downloading_not_completed');
            d.addClassName('end_downloading_process');
        });

        $$("#row_amgeoip_download_import_download_import_button .import .bubble").each(function(d){
            d.innerHTML = 'Import';
        });

        var requestDownloading = new Ajax.Request(
            startDownloadingUrl,
            {
                method: 'post',
                onSuccess: function(transport){

                    var response = eval('(' + transport.responseText + ')');

                    if (response.status == 'finish_downloading') {
                        _caller.doneDownloading(startUrl, processUrl, commitUrl, input, processer)
                    } else if (response.error){
                        $$(".am_processer_container .end_downloading_process").each(function(d){
                            d.removeClassName('end_downloading_process');
                            d.addClassName('end_downloading_not_completed');
                        });
                        processer.each(function(d){
                            d.down('span').innerHTML = '';
                        });
                        $$("#row_amgeoip_download_import_download_import_button .import .bubble").each(function(d){
                            d.innerHTML = 'Error';
                        });
                        processer.each(function(d){
                            d.setStyle({'width': '0%'});
                        });
                        alert(response.error);
                        _caller.error(response.error);
                    }
                }
            }
        );

    },

    doneDownloading: function(startUrl, processUrl, commitUrl, input, processer){
        var _caller = this;
        if (isDownloadStarted) {
             processer.each(function(d){
                d.setStyle({
                  'width': '100%'
              });
            });

            processer.each(function(d){
                d.down('span').innerHTML = '';
            });

            $$(".am_processer_container .end_downloading_process").each(function(d){
                d.removeClassName('end_downloading_process');
                d.addClassName('end_downloading_completed');
            });

            _caller.run(startUrl, processUrl, commitUrl, input);
            _caller.run(prevStartUrl, prevProcessUrl, prevCommitUrl, input);
        } else {
            processer.each(function(d){
                d.setStyle({
                  'width': '60%'
              });
            });

            processer.each(function(d){
                d.down('span').innerHTML = '1/2';
            });
            prevStartUrl = startUrl;
            prevProcessUrl = processUrl;
            prevCommitUrl = commitUrl;

            isDownloadStarted = true;
        }
    },

    run: function(startUrl, processUrl, commitUrl, input){
            var _caller = this;

            $$(".completed_import .bubble").each(function(d){
                d.innerHTML = 'Completed';
            });
            $$(".completed .bubble").each(function(d){
                d.innerHTML = 'Completed';
            });

            var request = new Ajax.Request(
                startUrl,
                {
                    method: 'post',
                    onSuccess: function(transport){
                        var response = eval('(' + transport.responseText + ')');


                        $$(".am_processer_container .end_imported").each(function(d){
                            d.removeClassName('end_imported');
                            d.addClassName('end_processing');
                        });

                        $$(".am_processer_container .end_not_imported").each(function(d){
                            d.removeClassName('end_not_imported');
                            d.addClassName('end_processing');
                        });

                        var processer = $$('div.am_processer');;

                        if (response.status == 'started'){

                            _caller.process(processUrl, commitUrl, processer);

                        } else if (response.error){
                            $$(".am_processer_container .end_processing").each(function(d){
                                d.removeClassName('end_processing');
                                d.addClassName('end_not_imported');
                            });
                            $$(".completed_import .bubble").each(function(d){
                                d.innerHTML = 'Error';
                            });
                            $$(".completed .bubble").each(function(d){
                                d.innerHTML = 'Error';
                            });
                            processer.each(function(d){
                                d.setStyle({'width': '0%'});
                            });
                            alert(response.error);
                            _caller.error(response.error);
                        }
                    }
                }
            );
        }
}
