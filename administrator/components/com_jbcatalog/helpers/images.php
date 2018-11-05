<?php
/*------------------------------------------------------------------------
# JBCatalog
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2014 JBCatalog.com. All Rights Reserved.
# @license - http://jbcatalog.com/catalog-license/site-articles/license.html GNU/GPL
# Websites: http://www.jbcatalog.com
# Technical Support:  Forum - http://jbcatalog.com/forum/
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

class ImagesHelper
{
    private static function filesize_get($file)
    {
        if (!file_exists($file)) return "File doesn't exist";

        $filesize = filesize($file);

        if ($filesize > 1024) {
            $filesize = ($filesize / 1024);

            if ($filesize > 1024) {
                $filesize = ($filesize / 1024);

                if ($filesize > 1024) {
                    $filesize = ($filesize / 1024);
                    $filesize = round($filesize, 1);
                    return $filesize . " GB";
                } else {
                    $filesize = round($filesize, 1);
                    return $filesize . " MB";
                }
            } else {
                $filesize = round($filesize, 1);
                return $filesize . " KB";
            }
        } else {
            $filesize = round($filesize, 1);
            return $filesize . "";
        }
    }

    public static function loaderUI($vals, $multi = true)
    {
        ?>
        <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
        <link rel="stylesheet" href="../components/com_jbcatalog/libraries/jsupload/css/jquery.fileupload-ui.css">
        <!-- CSS adjustments for browsers with JavaScript disabled -->
        <noscript>
            <link rel="stylesheet"
                  href="../components/com_jbcatalog/libraries/jsupload/css/jquery.fileupload-ui-noscript.css">
        </noscript>
        <!-- The file upload form used as target for the file upload widget -->
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="fileupload-buttonbar">
            <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                                <i class="icon-plus icon-white"></i>
                                <span>Přidat obrázek</span>
                                <input type="file" name="files[]" multiple>
                            </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Zahájit nahrávání</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Zrušit nahrávání</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Smazat</span>
                </button>
                <input type="checkbox" class="toggle">
                <!-- The loading indicator is shown during file processing -->
                <span class="fileupload-loading"></span>
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0"
                     aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <div class="clearfix"></div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped">
            <tbody class="files">
            <?php

            if (count($vals)) {
                foreach ($vals as $val) {
                    ?>
                    <tr class="template-download fade in">
                        <td>
                                            <span class="preview">

                                                <a href="../images/files/<?php echo $val ?>" title="<?php echo $val ?>"
                                                   download="<?php echo $val ?>" data-gallery=""><img
                                                        src="../images/files/thumbnail/<?php echo $val ?>"></a>

                                            </span>
                        </td>
                        <td>
                            <p class="name">
                                <a href="../images/files/<?php echo $val ?>" title="<?php echo $val ?>"
                                   download="<?php echo $val ?>" data-gallery=""><?php echo $val ?></a>
                                <input type="hidden" name="filnm[]" value="<?php echo $val ?>">
                            </p>


                        </td>
                        <td>
                                            <span class="size">
                                                <?php echo ImagesHelper::filesize_get("../images/files/" . $val) ?>
                                            </span>
                        </td>
                        <td>
                            <button class="btn btn-danger delete" data-type="DELETE"
                                    data-url="../images/?file=<?php echo $val ?>">
                                <i class="icon-trash icon-white"></i>
                                <span>Smazat</span>
                            </button>
                            <input type="checkbox" name="delete" value="1" class="toggle">
                        </td>
                    </tr>
                    <?php

                }
            }
            ?>
            </tbody>
        </table>


        <!-- The template to display files available for upload -->
        <script id="template-upload" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
                <tr class="template-upload fade">
                    <td>
                        <span class="preview"></span>
                    </td>
                    <td>
                        <p class="name">{%=file.name%}</p>

                        {% if (file.error) { %}
                            <div><span class="label label-important">Error</span> {%=file.error%}</div>
                        {% } %}
                    </td>
                    <td>
                        <p class="size">{%=o.formatFileSize(file.size)%}</p>
                        {% if (!o.files.error) { %}
                            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
                        {% } %}
                    </td>
                    <td>
                        {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                            <button class="btn btn-primary start">
                                <i class="icon-upload icon-white"></i>
                                <span>Start</span>
                            </button>
                        {% } %}
                        {% if (!i) { %}
                            <button class="btn btn-warning cancel">
                                <i class="icon-ban-circle icon-white"></i>
                                <span>Cancel</span>
                            </button>
                        {% } %}
                    </td>
                </tr>
            {% } %}


        </script>
        <!-- The template to display files available for download -->
        <script id="template-download" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
                <tr class="template-download fade">
                    <td>
                        <span class="preview">
                            {% if (file.thumbnailUrl) { %}
                                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                            {% } %}
                        </span>
                    </td>
                    <td>
                        <p class="name">
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                            <input type="hidden" name="filnm[]" value="{%=file.name%}" />
                        </p>

                        {% if (file.error) { %}
                            <div><span class="label label-important">Error</span> {%=file.error%}</div>
                        {% } %}
                    </td>
                    <td>
                        <span class="size">{%=o.formatFileSize(file.size)%}</span>
                    </td>
                    <td>
                        <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                            <i class="icon-trash icon-white"></i>
                            <span>Delete</span>
                        </button>
                        <input type="checkbox" name="delete" value="1" class="toggle">
                    </td>
                </tr>
            {% } %}


        </script>
        <script src="../components/com_jbcatalog/libraries/jsupload/js/vendor/jquery.ui.widget.js"></script>
        <!-- The Templates plugin is included to render the upload/download listings -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/tmpl.min.js"></script>
        <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/load-image.min.js"></script>
        <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/jquery.iframe-transport.js"></script>
        <!-- The basic File Upload plugin -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/jquery.fileupload.js"></script>
        <!-- The File Upload processing plugin -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/jquery.fileupload-process.js"></script>
        <!-- The File Upload image preview & resize plugin -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/jquery.fileupload-image.js"></script>
        <!-- The File Upload validation plugin -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/jquery.fileupload-validate.js"></script>
        <!-- The File Upload user interface plugin -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/jquery.fileupload-ui.js"></script>
        <!-- The main application script -->
        <script src="../components/com_jbcatalog/libraries/jsupload/js/main.js"></script>
        <?php

    }

}
