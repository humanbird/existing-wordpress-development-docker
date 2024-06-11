<?php

use Easy_Media_Replace_Helper as Helper;

/**
 * Replace file dialog
 *
 *
 * @link       http://nabillemsieh.com
 * @since      0.1.0
 *
 * @package    Easy_Media_Replace
 * @subpackage Easy_Media_Replace/admin/partials
 */
?>

<div id="emr__dialog" title="<?php echo Helper::trans('Upload a new file') ?>" class="dropzone" data-max-upload-size="<?php echo $max_upload_size ?>">
        <div class="dz-message">
                <p class="emr__dialog-dragzone-title">Drop file here.</p>
                <div class="emr__dialog-dragzone-or"><span>or</span></div>
                <button type="button" class="button-secondary emr__dialog-dragzone-select-btn">Select file</button>
                <div class="emr__dialog-dragzone-maxfile"><span><?php echo Helper::trans(sprintf("Maximum upload file size: %s", size_format(wp_max_upload_size()))) ?></span></div>
        </div>
</div>
</div>

<div class="emr__dialog-file-preview-container">
        <div class="emr__dialog-file-preview">
                <div class="dz-preview dz-file-preview">
                        <div class="emr__dialog-notice notice hidden"></div>
                        <div class="dz-image">
                                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                                <img data-dz-thumbnail />
                        </div>
                        <div class="dz-details">
                                <div class="dz-filename"><span data-dz-name></span></div>
                                <div class="dz-dimen"><span></span></div>
                                <div class="dz-size" data-dz-size></div>
                        </div>
                </div>
                <div class="emr__dialog-options">
                        <h3>Options</h3>
                        <div class="field">
                                <label for="regen-metadata">
                                        <input type="checkbox" name="options[regen_metadata]" id="regen-metadata" checked>Re-generate thumbnails.</label>
                                <p class="description">
                                        This option will remove the attachment's thumbnails and regenerate new ones for the uploaded image.
                                </p>
                        </div>
                        <div class="field">
                                <label for="regen-metdata">
                                        <input type="checkbox" name="options[modified_time]" id="regen-metdata" checked>Update the modified time.</label>
                        </div>
                </div>
        </div>
</div>