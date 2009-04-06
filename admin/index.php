<?php

/**
   Frog CMS - Content Management Simplified. <http://www.madebyfrog.com>
   Copyright (C) 2008 Philippe Archambault <philippe.archambault@gmail.com>

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//  Constantes  --------------------------------------------------------------

define('FROG_VERSION', '0.9.5 RC2');

define('FROG_ROOT', dirname(__FILE__).'/..');
define('CORE_ROOT', FROG_ROOT.'/frog');

require_once(CORE_ROOT.'/utils.php');

$config_file = FROG_ROOT.'/config.php';

// Security checks -----------------------------------------------------------
if (isWritable($config_file)) {
    // Windows systems always have writable config files... skip those.
    if (substr(PHP_OS, 0, 3) != 'WIN') {
        echo '<h1>Frog CMS automatically disabled!</h1>';
        echo '<p>Frog CMS has been disabled as a security precaution.</p>';
        echo '<p><strong>Reason:</strong> the configuration file was found to be writable.';
        exit();
    }
}

require_once(CORE_ROOT.'/utils.php');

define('APP_PATH',  CORE_ROOT.'/app');

define('SESSION_LIFETIME', 3600);
define('REMEMBER_LOGIN_LIFETIME', 1209600); // two weeks

define('DEFAULT_CONTROLLER', 'page');
define('DEFAULT_ACTION', 'index');

define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIE_SECURE', false);

require_once($config_file);

define('BASE_URL', URL_PUBLIC . (endsWith(URL_PUBLIC, '/') ? '': '/') . ADMIN_DIR . (endsWith(ADMIN_DIR, '/') ? '': '/') . (USE_MOD_REWRITE ? '': '?/'));

require CORE_ROOT.'/Framework.php';


//  Database connection  -----------------------------------------------------

if (USE_PDO)
{
    $__FROG_CONN__ = new PDO(DB_DSN, DB_USER, DB_PASS);
    if ($__FROG_CONN__->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql')
        $__FROG_CONN__->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
}
else
{
    require_once CORE_ROOT . '/libraries/DoLite.php';
    $__FROG_CONN__ = new DoLite(DB_DSN, DB_USER, DB_PASS);
}

Record::connection($__FROG_CONN__);
Record::getConnection()->exec("set names 'utf8'");


//  Initialize  --------------------------------------------------------------

Setting::init();

use_helper('I18n');
I18n::setLocale(Setting::get('language'));

Plugin::init();

//  Get controller and action and execute  -----------------------------------

Dispatcher::dispatch(null, Setting::get('default_tab'));
