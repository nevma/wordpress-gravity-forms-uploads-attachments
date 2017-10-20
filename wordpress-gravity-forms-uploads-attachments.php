<?php

    /*
        Plugin Name:       Gravity Forms uploads attachments
        Plugin URI:        https://github.com/nevma/wordpress-gravity-forms-uploads-attachments
        Description:       Adds file uploads of WordPress Gravity Forms submissions as attachments to email notifications.
        Version:           0.9.2
        Author:            Nevma
        Author URI:        https://nevma.gr/
        License:           GPL-2.0+
        License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
        Text Domain:       wordpress-gravity-forms-uploads-attachments
        GitHub Plugin URI: https://github.com/nevma/wordpress-gravity-forms-uploads-attachments
    */



    /**
     * Copyright 2015 Nevma (nevma.gr, info@nevma.gr)
     *
     * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General 
     * Public License, version 2, as published by the Free Software Foundation. This program is distributed in the hope
     * that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
     * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License along with this program; if not, write to the 
     * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA.
     * 
     * GPL: http://www.gnu.org/copyleft/gpl.html
     */



    // Do some security checks.

    if ( ! defined( 'WPINC' ) ) {
    	die;
    }

    if ( ! defined( 'ABSPATH' ) ) {
        die;
    }



    /**
     * Adds file uploads as attachments to the notification of a form's submission. 
     */

    function nvm_wpgfua_notification_attachments( $notification, $form, $entry ) {

        $log = 'nvm_wpgfua_notification_attachments() - ';
        
        GFCommon::log_debug( $log . 'starting.' );
     
        if ( $notification['name'] == 'Admin Notification' ) {

            // Check if there exist any file upload fields. 
     
            $fileupload_fields = GFCommon::get_fields_by_type( $form, array( 'fileupload' ) );
     
            if ( ! is_array( $fileupload_fields ) ) {
                return $notification;
            }

            $notification['attachments'] = rgar( $notification, 'attachments', array() );
            $upload_root = RGFormsModel::get_upload_root();

            // Process all possible uploaded files.
     
            foreach( $fileupload_fields as $field ) {
     
                $url = rgar( $entry, $field->id );
     
                if ( empty( $url ) ) {

                    continue;

                } elseif ( $field->multipleFiles ) {

                    // Multiple file upload case.

                    $uploaded_files = json_decode( stripslashes( $url ), true );

                    foreach ( $uploaded_files as $uploaded_file ) {

                        $attachment = preg_replace( '|^(.*?)/gravity_forms/|', $upload_root, $uploaded_file );
                        GFCommon::log_debug( $log . 'attaching the file: ' . print_r( $attachment, true  ) );
                        $notification['attachments'][] = $attachment;

                    }

                } else {

                    // Single file upload case.

                    $attachment = preg_replace( '|^(.*?)/gravity_forms/|', $upload_root, $url );
                    GFCommon::log_debug( $log . 'attaching the file: ' . print_r( $attachment, true  ) );
                    $notification['attachments'][] = $attachment;

                }
     
            }
     
        }
     
        GFCommon::log_debug( $log . 'stopping.' );
     
        return $notification;

    }

    // Register the function which adds uploads as email notification attachments. 

    add_filter( 'gform_notification', 'nvm_wpgfua_notification_attachments', 10, 3 );

?>