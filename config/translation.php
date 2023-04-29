<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

!defined('DAO_LANG_EN') && define('DAO_LANG_EN', 'en');
!defined('DAO_LANG_CN') && define('DAO_LANG_CN', 'zh-cn');

/**
 * Multilingual configuration
 */
return [
    // Default language
    'locale' => getenv('LANG_DEFAULT_LANG') ?? DAO_LANG_CN,
    // Fallback language
    'fallback_locale' => [DAO_LANG_CN, DAO_LANG_EN],
    // Folder where language files are stored
    'path' => base_path() . '/resource/translations',
];