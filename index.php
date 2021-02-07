<?php
/*
Plugin Name: FTP Webcam Image Downloader
Description: Semplice downloader di file via ftp, Puoi trovare la configurazione nel menu impostazioni
alla voce: Webcam Downloader Option
Version:     1.2
Author:      Gabriele Fontana <gafreax@gmail.com>
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Simple Image Downloader from FTP
Copyright (C) 2021  Gabriele Fontana

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/


error_log('------  FTP Webcam Image Downloader ----  ');
const MSGLOG = 'FTP Webcam Image Downloader';
function download_image($username, $port, $passive, $server, $remotefile, $password, $localfile)
{
    $connection = ftp_connect($server, $port);
    if (!$connection) {
        throw new ErrorException('can not connect');
    }

    $login = ftp_login($connection, $username, $password);
    if (!$login) {
        throw new ErrorException('can not login with ' . $login);
    }
    if ($passive) {
        ftp_pasv($connection, true);
    }
    $up = wp_upload_dir();
    $ftpfile = ftp_get($connection, $up['basedir']  . "/" .  $localfile, $remotefile, FTP_BINARY);
    if (!$ftpfile) {
        throw new ErrorException('can not login with ' . $login);
    }
    error_log(MSGLOG . 'Successfully written to '. $localfile);
    if ($connection) {
        ftp_close($connection);
    }
}

function download_image_hooks()
{
    try {
        $server = get_option('webcam_downloader_ftp_server');
        $username = get_option('webcam_downloader_ftp_username');
        $password = get_option('webcam_downloader_ftp_password');
        $remotefile = get_option('webcam_downloader_ftp_remotefile');
        $localfile = get_option('webcam_downloader_ftp_localfile');
        $port = get_option('webcam_downloader_ftp_port');
        $passive = get_option('webcam_downloader_ftp_passive');
        if (empty($server) ||empty($localfile)|| empty($remotefile)||empty($password)||empty($username)) {
            throw new InvalidArgumentException('server not defined');
        }
        download_image($username, $port, $passive, $server, $remotefile, $password, $localfile);
    } catch (exception $e) {
        wp_clear_scheduled_hook('webcam_downloader_cron_hook');
        $msg = MSGLOG . ' Exception - ';
        error_log($msg . $e);
        error_log($msg . $remotefile);
        error_log($msg . $remotefile);
        error_log($msg . $username);
        error_log($msg . $password);
        error_log($msg . $localfile);
        error_log($msg . $server);
    }
}

function myplugin_register_settings()
{
    error_log(MSGLOG .  '- Register Settings');
    add_option('webcam_downloader_ftp_localfile');
    add_option('webcam_downloader_ftp_passive', false);
    add_option('webcam_downloader_ftp_password');
    add_option('webcam_downloader_ftp_port', 21);
    add_option('webcam_downloader_ftp_remotefile');
    add_option('webcam_downloader_ftp_scheduling');
    add_option('webcam_downloader_ftp_server');
    add_option('webcam_downloader_ftp_username');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_localfile');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_passive');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_password');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_port');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_remotefile');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_scheduling');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_server');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_username');
}

function myplugin_register_options_page()
{
    error_log(MSGLOG . ' - Add option pages');
    add_options_page('Webcam Downloader', 'Webcam Downloader Option', 'manage_options', 'myplugin', 'myplugin_options_page');
}

function myplugin_options_page()
{?>
    <div>
        <?php screen_icon(); ?>
        <h2>Webcam Downloader</h2>
        <p>
        <img  height="200px" src="/wp-content/uploads/<?= get_option('webcam_downloader_ftp_localfile') ?>" alt="image downloaded" />
        <br />
        <?= '/wp-content/uploads/'. get_option('webcam_downloader_ftp_localfile') ?>
         </p>
        <form method="post" action="options.php">
            <?php settings_fields('myplugin_options_group'); ?>
            <h3>Opzioni</h3>
            <table>
            <caption>Imposta i dati per accedere al server ftp della webcam</caption>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_server">Server</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_server" name="webcam_downloader_ftp_server" value="<?= get_option('webcam_downloader_ftp_server') ?>" /></td>
                </tr>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_username">Username</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_username" name="webcam_downloader_ftp_username" value="<?= get_option('webcam_downloader_ftp_username') ?>" /></td>
                </tr>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_password">Password</label></th>
                    <td><input type="password" id="webcam_downloader_ftp_password" name="webcam_downloader_ftp_password" value="<?= get_option('webcam_downloader_ftp_password') ?>" /></td>
                </tr>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_port">Porta</label></th>
                    <td><input type="number" id="webcam_downloader_ftp_port" name="webcam_downloader_ftp_port" value="<?= get_option('webcam_downloader_ftp_port') ?>" /></td>
                </tr>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_passive">Passive</label></th>
                    <td><input type="checkbox" id="webcam_downloader_ftp_passive" name="webcam_downloader_ftp_passive" value="<?= get_option('webcam_downloader_ftp_passive') ?>" /></td>
                </tr>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_remotefile">File Remoto</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_remotefile" name="webcam_downloader_ftp_remotefile" value="<?= get_option('webcam_downloader_ftp_remotefile') ?>" /></td>
                </tr>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_localfile">File locale</label></th>
                    <td>
                          <input type="text" id="webcam_downloader_ftp_localfile" name="webcam_downloader_ftp_localfile" value="<?= get_option('webcam_downloader_ftp_localfile') ?>" />
                    </td>
                </tr>
                <tr >
                    <th scope="row"><label for="webcam_downloader_ftp_scheduling">Minuti per la verifica</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_scheduling" name="webcam_downloader_ftp_scheduling" value="<?= get_option('webcam_downloader_ftp_scheduling') ?>" /></td>
                </tr>

            </table>
            <?php submit_button(); ?>
        </form>
                   
    </div>
<?php
}

function webcam_add_cron_interval($schedules)
{
    $scheduling = get_option('webcam_downloader_ftp_scheduling');
    if (empty($scheduling)) {
        error_log(MSGLOG . ' - scheduling not defined');
        return $schedules;
    }
    $schedules['webcam_downloader_schedules'] = array(
        'interval' => 60 * $scheduling,
        'display'  => esc_html__('Every Some times'),
    );
    return $schedules;
}
if (!wp_next_scheduled('webcam_downloader_cron_hook')) {
    wp_schedule_event(time(), 'webcam_downloader_schedules', 'download_image');
}

register_deactivation_hook(__FILE__, 'bl_deactivate');
 
function bl_deactivate()
{
    $timestamp = wp_next_scheduled('webcam_downloader_cron_hook');
    wp_unschedule_event($timestamp, 'webcam_downloader_cron_hook');
}

add_action('admin_init', 'myplugin_register_settings');
add_action('admin_menu', 'myplugin_register_options_page');
add_action('added_option', 'download_image_hooks');
add_action('updated_option', 'download_image_hooks');
add_action('added_option', 'webcam_add_cron_interval');
add_action('updated_option', 'webcam_add_cron_interval');
add_filter('cron_schedules', 'webcam_add_cron_interval');
