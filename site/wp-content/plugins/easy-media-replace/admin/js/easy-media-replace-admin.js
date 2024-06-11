(function ($) {
    'use strict';

    // Manage dialog notices.
    var Notice = {
        // Notice element selector.
        selector: '.emr__dialog-notice',
        // Show error notice.
        error: function (message) {
            var el = $(this.selector);
            el.text(message);
            el.removeClass('notice-success');
            el.addClass('notice-error');
            this.show(el);
        },
        /**
         * Show success notice.
         * @param {string} message
         */
        success: function (message) {
            var el = $(this.selector);
            el.text(message);
            el.removeClass('notice-error');
            el.addClass('notice-success');

            this.show(el);
        },
        /**
         * Hide notice.
         * @param {Object} el
         */
        hide: function (el) {
            (el || $(this.selector)).addClass('hidden');
        },
        /**
         * Hide notice.
         * @param {Object} el
         */
        show: function (el) {
            (el || $(this.selector)).removeClass('hidden');
        }
    };

    // Handle file replacement.
    var EMR = {
        /**
         Replace button/link element .
         */
        triggerElement: null,

        /**
         * Replace dialog selector.
         */
        dialogSelector: '#emr__dialog',

        /**
         * Process replacement button selector.
         */
        replaceBtnSelector: '#emr__dialog-replace-btn',
        /**
         * jQuery UI dialog object.
         */
        dialog: null,

        /**
         * Dropzone object.
         */
        dropzone: null,

        /**
         * Initial max upload size.
         * The value is updated once the dialog is attached to DOM.
         */
        maxUploadSize: -1,

        /**
         * Data required to process upload/replace actions.
         */
        data: {
            attachment: null,
            new_id: null
        },

        /**
         * Ajax actions constants.
         */
        actions: {
            replace: 'emr:replace',
            dialog: 'emr:dialog',
            upload: 'emr:upload',
            remove: 'emr:remove'
        },

        /**
         * Load dialog html for given mimeType
         * and attach it to DOM.
         * @param {function} success
         * @param {function} error
         */
        loadDialog: function (success, error) {
            var _this = this;
            var data = {
                _ajax_nonce: emr_ajax_object._ajax_nonce,
                action: _this.actions.dialog,
                mime: _this.data.attachment.mime
            };
            var url = emr_ajax_object.ajax_url;
            var _error = "Unable to load dialog. Please, try again.";
            $.get(url, data, function (content) {
                $('body').append(content);
                if (_this.isDialogAttached()) success();
                else error(_error);
            }, 'html').fail(function () {
                error(_error);
            });
        },
        setAttachment: function (success, error) {
            if (this.triggerElement.data('attachment-id') && this.triggerElement.data('attachment-mime')) {
                this.data.attachment = {
                    id: this.triggerElement.data('attachment-id'),
                    mime: this.triggerElement.data('attachment-mime')
                };
                success();
            } else {
                try {
                    var attachmentCollection = wp.media.frame.state().get('selection').first();
                    this.data.attachment = attachmentCollection.toJSON();
                    success();
                    if (!this.data.attachment || !this.data.attachment.id) {
                        error("Not attachment selected.");
                    }
                } catch (err) {
                    try {
                        this.data.attachment = wp.media.frame.model.attributes;
                        success();
                        if (!this.data.attachment || !this.data.attachment.id) {
                            error("Not attachment selected.");
                        }
                    } catch (err) {
                        alert(err.message || "Not attachment selected.");
                    }
                }
            }
        },
        isDialogAttached: function () {
            return $(this.dialogSelector).length > 0;
        },

        /**
         * Attach dialog to DOM and open it.
         */
        openDialog: function () {
            this.setAttachment((function () {
                // Maybe attach dialog to DOM.
                if (!this.isDialogAttached()) {
                    var _this = this;
                    var $spinner = _this.triggerElement.next('.spinner');
                    _this.loadDialog(function () {
                        _this.maxUploadSize = +$(_this.dialogSelector).data('max-upload-size') || 0;
                        $spinner.remove();
                        _this.openDialog();
                    }, function (err) {
                        $spinner.remove();
                        alert(err);
                    });
                } else {
                    this.triggerElement.next('.spinner').remove();
                    // Dialog is attached but not intialized yet.
                    if (!this.dialog) {
                        this.initializeDialog();
                    }
                    if (!this.dropzone || this.dropzone.files.length === 0) {
                        this.disableReplaceBtn(true);
                    }

                    // Fires dialog `open` event.
                    this.dialog.dialog('open');
                }
            }).bind(this), function (err) {
                alert(err);
            });
        },
        initializeDialog: function () {
            var _this = this;
            this.dialog = $(this.dialogSelector).dialog({
                autoOpen: false,
                dialogClass: 'emr__dialog',
                height: 'auto',
                width: 'auto',
                resizable: false,
                draggable: false,
                modal: true,
                buttons: [
                    {
                        id: _this.replaceBtnSelector.slice(1),
                        class: "button button-large button-primary",
                        text: "Replace",
                        click: function () {
                            Notice.hide(null);
                            var data = {
                                _ajax_nonce: emr_ajax_object._ajax_nonce,
                                old_id: _this.data.attachment.id,
                                new_id: _this.data.new_id,
                                action: _this.actions.replace,
                                // improve: serialize form instead
                                regen_thumbs: $(_this.dialogSelector).find('input[name="options[regen_metadata]"]').is(':checked'),
                                modified_time: $(_this.dialogSelector).find('input[name="options[modified_time]"]').is(':checked')
                            };
                            $.post(emr_ajax_object.ajax_url, data, function (response) {
                                if (response.success) {
                                    _this.dropzone.removeAllFiles();
                                    _this.dialog.dialog('close');
                                    try {
                                        wp.media.frame.content.get().collection.props.set({ ignore: (+new Date()) });
                                    } catch (error) {
                                        location.reload();
                                    }
                                    Notice.success(response.data.message);
                                }
                            }, 'json').fail(function (response) {
                                Notice.error(response.responseJSON.data.message);
                            });
                        }
                    },
                    {
                        id: "emr__dialog-cancel-btn",
                        class: "button button-large button-secondary",
                        text: "Cancel",
                        click: (function () {
                            this.dialog.dialog("close");
                        }).bind(this)
                    }
                ],
                open: (function () {
                    this.maybeInitializeDropzone();
                }).bind(this)
            });
        },
        /**
         * Initialize or update Dropzone object.
         */
        maybeInitializeDropzone: function () {
            var _this = this;

            if (_this.dropzone) {
                _this.dropzone.hiddenFileInput.accept = _this.dropzone.options.acceptedFiles = _this.data.attachment.mime;
                return;
            }
            Dropzone.autoDiscover = false;
            _this.dropzone = new Dropzone(_this.dialogSelector, {
                /**
                 * Upload URL
                 */
                url: emr_ajax_object.ajax_url + '?action=' + _this.actions.upload,
                paramName: 'async-upload',
                previewTemplate: $('.emr__dialog-file-preview-container').html(),
                /**
                 * Only process one file.
                 */
                parallelUploads: 1,

                /**
                 * Only one file to upload.
                 */
                maxFiles: 1,

                /**
                 * Maximum file size allowed.
                 */
                maxFilesize: this.maxUploadSize,

                /**
                 * Accepted mimeTypes.
                 */
                acceptedFiles: this.data.attachment.mime,

                /**
                 * Show remove links.
                 */
                addRemoveLinks: true,

                /**
                 * Additional request data.
                 */
                params: { _ajax_nonce: emr_ajax_object._ajax_nonce },

                /**
                 * Request timeout
                 */
                timeout: 180000,

                /**
                 * Hide notice before sending file if applicable
                 */
                sending: function () {
                    _this.disableReplaceBtn(true);
                    Notice.hide();
                },

                /**
                 * Set new file path.
                 *
                 * @param {Object} file
                 * @param {Object} response
                 */
                success: function (file, response) {

                    if (response.success) {
                        _this.data.new_id = response.data.new_id;
                        if (this.files.length === 1) {
                            _this.disableReplaceBtn(false);
                        }
                    }
                },
                /**
                 * Handle upload failure error,
                 * or max file is exceeded.
                 * @param {Object} file
                 * @param {Object} response
                 */
                error: function (file, response) {
                    var message = "Unable to upload file. Please, try again.";
                    if (file.accepted && response.data) {
                        message = response.data ? response.data.message : message;
                    } else if (!file.accepted) {
                        message = response || message;
                    }
                    Notice.error(message);
                }
            });

            this.dropzone.on('addedfile', function (file) {
                if (this.files.length === 2) {
                    this.removeFile(this.files[1]);
                }

                var dialogElement = $(_this.dialogSelector);
                dialogElement.find('.dz-preview').each(function () {
                    $(this).find('.dz-size').after($(this).parent().find('.dz-remove'));
                });
                _this.dropzone.on('thumbnail', function (file) {
                    dialogElement.find('.dz-preview .dz-dimen span').text(file.width + ' x ' + file.height);
                });
            });

            // On remove click, send request to server to delete uploaded file.
            // then disable replace button.
            this.dropzone.on('removedfile', function () {
                _this.removeUploadedFile();
                _this.disableReplaceBtn(true);
            });
        },

        /**
         * Toggle replace button state.
         * @param boolean disable
         */
        disableReplaceBtn: function (disable) {
            $(this.replaceBtnSelector).attr('disabled', disable);
        },

        /**
         * Remove uploaded file from server.
         */
        removeUploadedFile: function () {
            var data = {
                action: this.actions.remove,
                id: this.data.new_id,
                _ajax_nonce: emr_ajax_object._ajax_nonce
            }
            $.post(emr_ajax_object.ajax_url, data);
        }
    };

    // Open replacement dialog.
    $('body').on('click', '.js-emr-open-dialog', function (e) {
        e.preventDefault();
        EMR.triggerElement = $(this);
        // Show loading spinner next to the button.
        EMR.triggerElement.after('<span class="spinner"></span>');
        EMR.openDialog();
    });
})(jQuery);
