<?php

/**
 * File uploader class. Could upload multiple files. Uses Zend_File_Transfer_Adapter_Http as superclass.
 * Zend_File_Transfer_Adapter_Http could not rename files after multiple upload. This bug was fixed here
 * (see _addRenameFilter() & _getRenameFilter()). Also were added methods to set validators that makes
 * uploader ussage more flexible.
 * 	 
 * @author Konstantin Shamko <konstantin.shamko@gmail.com> 
 * @version 0.0.1
 * @copyright  Copyright (c) 2009 Konstantin Shamko
 * @category VaselinEngine
 * @package Bel Classes
 * @license  New BSD License
 * 
 */
class Kps_File_Uploader extends Zend_File_Transfer_Adapter_Http {

    /**
     * Set allowed file extensions
     *
     * @param array $extensions - array with extensions
     * @param  string|array $files - Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setAllowedExtensions($extensions, $files = null) {
        return $this->addValidator('Extension', false, $extensions, $files);
    }

    /**
     * Set forbidden file extensions
     *
     * @param array $extensions - array with extensions
     * @param  string|array $files - Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setForbiddenExtensions($extensions, $files = null) {
        return $this->addValidator('ExcludeExtension', false, $extensions, $files);
    }

    /**
     * Set allowed file mime types
     *
     * @param array $extensions - array with extensions
     * @param  string|array $files - Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setAllowedMimeTypes($mimetypes, $files = null) {
        return $this->addValidator('MimeType', false, $mimetypes, $files);
    }

    /**
     * Sets forbidden file mime types
     *
     * @param array $extensions - array with extensions
     * @param  string|array $files - Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setForbiddenMimeTypes($mimetypes, $files = null) {
        return $this->addValidator('ExcludeMimeType', false, $mimetypes, $files);
    }

    /**
     * Set file size
     *
     * @param integer $min - min diskspace for $files
     * @param integer $max - max diskspace for $files
     * @param  string|array $files - Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setFileSize($min = null, $max = null, $files = null) {
        return $this->addValidator('FilesSize', false, array('min' => $min, 'max' => $max), $files);
    }

    /**
     * Will check if $files are images
     *
     * @param  string|array $files - Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function isImage($files = null) {
        return $this->addValidator('isImage', false, null, $files);
    }

    /**
     * Will check min & max image size
     *
     * @param array $min - array with min image dimensions (array(width, height))
     * @param array $max - array with max image dimensions (array(width, height))
     * @param  string|array $files - Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setImageSize($min = null, $max = null, $files) {
        return addValidator('ImageSize', false, array('minwidth' => $min[0], 'minheight' => $min[1], 'maxwidth' => $max[0], 'maxheight' => $max[1]), $files);
    }

    /**
     * Set ignore ot not when files where not sent by form
     *
     * @param bool $ignore - true to ignore when files where not sent by form
     * @param string|array $files   (Optional) Files to set the options for
     * @return Zend_File_Transfer_Adapter     * 
     */
    public function ignoreNoFile($ignore, $files = null) {
        return $this->setOptions(array('ignoreNoFile' => $ignore), $files);
    }

    /**
     * Upload files
     *
     * @param bool $rename - if true files will be renamed after upload with unique random names
     * @return bool - true if ok
     */
    public function upload($rename = true) {
        if ($rename) {
            foreach ($this->getFileInfo() as $key => $_file) {
                $this->_addRenameFilter(array('target' => $_file['destination'] . '/' . rand(1000, 9999) . '_' . time() . '_' . basename($_file['name']), 'overwrite' => true), $key);
            }
        }
        return $this->receive();
    }

    /**
     * Adds rename filter for this class
     *
     * @param  string|array $options   Options to set for the filter
     * @param  string|array $files     Files to limit this filter to
     * @return Zend_File_Transfer_Adapter
     */
    private function _addRenameFilter($options = null, $files = null) {
        $filter = new Zend_Filter_File_Rename($options);
        $files = $this->_getFiles($files, true, true);
        foreach ($files as $file) {
            $this->_filters['Zend_Filter_File_Rename'][$file] = $filter;
            $this->_files[$file]['filters'][] = 'Zend_Filter_File_Rename';
        }
        return $this;
    }

    /**
     * Retrieve individual rename filter
     *
     * @param  string $file - file to get rename folter for
     * @return Zend_Filter_Interface|null
     */
    private function _getRenameFilter($file) {
        return $this->_filters['Zend_Filter_File_Rename'][$file];
    }

    /**
     * Receive the file from the client (Upload). Don't use it to upload files. Use upload() method
     * instead. receive() method was written here to fix bug with files renaming after upload.
     *
     * @param  string|array $files (Optional) Files to receive
     * @return bool
     */
    public function receive($files = null) {
        if (!$this->isValid($files)) {
            return false;
        }

        $check = $this->_getFiles($files);
        foreach ($check as $file => $content) {
            if (!$content['received']) {
                $directory = '';
                $destination = $this->getDestination($file);
                if ($destination !== null) {
                    $directory = $destination . DIRECTORY_SEPARATOR;
                }

                $filename = $directory . $content['name'];
                $rename = $this->_getRenameFilter($file);
                if ($rename !== null) {
                    $filename = $rename->getNewName($content['tmp_name']);
                    $key = array_search(get_class($rename), $this->_files[$file]['filters']);
                    unset($this->_files[$file]['filters'][$key]);
                }

                // Should never return false when it's tested by the upload validator
                if (!move_uploaded_file($content['tmp_name'], $filename)) {
                    if ($content['options']['ignoreNoFile']) {
                        $this->_files[$file]['received'] = true;
                        $this->_files[$file]['filtered'] = true;
                        continue;
                    }

                    $this->_files[$file]['received'] = false;
                    return false;
                }

                if ($rename !== null) {
                    $this->_files[$file]['destination'] = dirname($filename);
                    $this->_files[$file]['name'] = basename($filename);
                }

                $this->_files[$file]['tmp_name'] = $filename;
                $this->_files[$file]['received'] = true;
            }

            if (!$content['filtered']) {
                if (!$this->_filter($file)) {
                    $this->_files[$file]['filtered'] = false;
                    return false;
                }

                $this->_files[$file]['filtered'] = true;
            }
        }

        return true;
    }
}