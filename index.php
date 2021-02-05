<?php
/*
Plugin Name: FTP Webcam Image Downloader
Description: 
Version:     1.0
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


error_log('---  FTP Webcam Image Downloader ----  ');
function download_image()
{
    $server = get_option('webcam_downloader_ftp_server');
    if (empty($server)) {
        error_log('gaf - server not defined');
        return;
    }
    $username = get_option('webcam_downloader_ftp_username');
    if (empty($username)) {
        error_log('gaf - username not defined');
        return;
    }
    $password = get_option('webcam_downloader_ftp_password');
    if (empty($password)) {
        error_log('gaf - password not defined');
        return;
    }
    $remotefile = get_option('webcam_downloader_ftp_remotefile');
    if (empty($remotefile)) {
        error_log('gaf - remotefile not defined');
        return;
    }
    $localfile = get_option('webcam_downloader_ftp_localfile');
    if (empty($localfile)) {
        error_log('gaf - localfile not defined');
        return;
    }
    $scheduling = get_option('webcam_downloader_ftp_scheduling');
    if (empty($scheduling)) {
        error_log('gaf - scheduling not defined');
        return;
    }
    error_log("gaf - remote " . $remotefile);
    error_log("gaf - local " . $localfile);
    error_log("gaf - sched " . $scheduling);
    try {
        $connection = ftp_connect($server);

        error_log("gaf - connection " . $connection);
        if ($connection !== false) {
            $login = ftp_login($connection, $username, $password);
            if (empty($login)) {
                error_log("gaf - No login");
                error_log("gaf - login " . $login);

                error_log("gaf - remote " . $remotefile);
                error_log("gaf - username " . $username);
                error_log("gaf - password " . $password);
                error_log("gaf - local " . $localfile);
                error_log("gaf - sched " . $scheduling);

                error_log("gaf - server " . $server);
                return;
            }
            while (true && $login) {
                $up = wp_upload_dir();
                $ftpfile = ftp_get($connection,  $up['basedir']  . "/" .  $localfile, $remotefile, FTP_BINARY);
                if ($ftpfile) {
                    error_log("Successfully written to $localfile\n");
                    if ($connection) {
                        ftp_close($connection);
                        sleep(60 * $scheduling);
                    }
                } else {
                    error_log("gaf - No conn");
                    error_log("gaf - basedir " . $up['basedir']);
                    error_log("gaf - server " . $server);
                    error_log("gaf - login " . $login);
                    error_log("gaf - ftpfile " . $ftpfile ? 'ok' : 'ko');
                    error_log("gaf - local " . $localfile);
                    error_log("gaf - sched " . $scheduling);
                }
            }
        } else {
            error_log("gaf - No conn");
        }
    } catch (exception $e) {
        error_log("gaf - " . $e);
    }
    return;
}

function myplugin_register_settings()
{
    error_log("gaf - Register Settings");
    add_option('webcam_downloader_ftp_server');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_server');
    add_option('webcam_downloader_ftp_username');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_username');
    add_option('webcam_downloader_ftp_password');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_password');
    add_option('webcam_downloader_ftp_remotefile');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_remotefile');
    add_option('webcam_downloader_ftp_localfile');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_localfile');
    add_option('webcam_downloader_ftp_scheduling');
    register_setting('myplugin_options_group', 'webcam_downloader_ftp_scheduling');
}
add_action('admin_init', 'myplugin_register_settings');

function myplugin_register_options_page()
{
    error_log("gaf - Add option pages");
    add_options_page('Webcam Downloader', 'Webcam Downloader Option', 'manage_options', 'myplugin', 'myplugin_options_page');
}


function myplugin_options_page()
{
?>
    <div>
        <?php screen_icon(); ?>
        <h2>Webcam Downloader</h2>
        <form method="post" action="options.php">
            <?php settings_fields('myplugin_options_group'); ?>
            <h3>Opzioni</h3>
            <table>
                <tr valign="top">
                    <th scope="row"><label for="webcam_downloader_ftp_server">Server</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_server" name="webcam_downloader_ftp_server" value="<?php echo get_option('webcam_downloader_ftp_server'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="webcam_downloader_ftp_username">Username</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_username" name="webcam_downloader_ftp_username" value="<?php echo get_option('webcam_downloader_ftp_username'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="webcam_downloader_ftp_password">Password</label></th>
                    <td><input type="password" id="webcam_downloader_ftp_password" name="webcam_downloader_ftp_password" value="<?php echo get_option('webcam_downloader_ftp_password'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="webcam_downloader_ftp_remotefile">File Remoto</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_remotefile" name="webcam_downloader_ftp_remotefile" value="<?php echo get_option('webcam_downloader_ftp_remotefile'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="webcam_downloader_ftp_localfile">File locale</label></th>
                    <td><img src="/wp-content/uploads/<?php echo get_option('webcam_downloader_ftp_localfile'); ?>" alt="image downloaded" /> <input type="text" id="webcam_downloader_ftp_localfile" name="webcam_downloader_ftp_localfile" value="<?php echo get_option('webcam_downloader_ftp_localfile'); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="webcam_downloader_ftp_scheduling">Ogni quanto vuoi verificare la nuova immagine</label></th>
                    <td><input type="text" id="webcam_downloader_ftp_scheduling" name="webcam_downloader_ftp_scheduling" value="<?php echo get_option('webcam_downloader_ftp_scheduling'); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}
add_action('admin_menu', 'myplugin_register_options_page');

//add_action('plugins_loaded', 'download_image');
add_action('added_option', 'download_image', 10, 2);
add_action('updated_option', 'download_image', 10, 3);


function webcam_add_cron_interval($schedules)
{
    $scheduling = get_option('webcam_downloader_ftp_scheduling');
    if (empty($scheduling)) {
        error_log('gaf - scheduling not defined');
        return $schedules;
    }
    $schedules['webcam_downloader'] = array(
        'interval' => 60 * $scheduling,
        'display'  => esc_html__('Every Some times'),
    );
    return $schedules;
}
if (!wp_next_scheduled('webcam_downloader_cron_hook')) {
    wp_schedule_event(time(), 'webcam_downloader_cron_hook', 'download_image');
}
// add_filter('cron_schedules', 'webcam_add_cron_interval');
