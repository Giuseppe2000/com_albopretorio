<?php
/**
*
* @package      COM_ALBOPRETORIO
* @copyright    Copyright (C) 2014 Alessandro Pasotti http://www.itopen.it All rights reserved.
* @license      GNU/AGPL

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
// No direct access to this file
defined('_JEXEC') or die('Restricted access');



if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

/**
 * Script file of GeoContent component
 */
class com_AlbopretorioInstallerScript
{

        function activatePlugins(){
            $db = JFactory::getDbo();

            // Activate albopretorio system plugin
            $db->setQuery("UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element='albopretorio' AND folder='system'");
            $db->execute();
        }

        function installOthers($parent) {
            $manifest = $parent->get("manifest");
            $parent = $parent->getParent();
            $source = $parent->getPath("source");

            $installer = new JInstaller();

            // Install plugins
            #require '/home/ale/.composer/vendor/autoload.php';
            #\Psy\Shell::debug(get_defined_vars());

            if($manifest->plugins){
                foreach($manifest->plugins->plugin as $plugin) {
                    $attributes = $plugin->attributes();
                    $plg = $source . DS . $attributes['folder'].DS.$attributes['plugin'];
                    $installer->install($plg);
                    echo "Plugin " . $attributes['plugin'] . " successfully installed!";
                }
            }

            // Install modules
            if($manifest->modules){
                foreach($manifest->modules->module as $module) {
                    $attributes = $module->attributes();
                    $mod = $source . DS . $attributes['folder'].DS.$attributes['module'];
                    $installer->install($mod);
                    echo "Module " . $attributes['module'] . " successfully installed!";
                }
            }
        }


        /**
         * method to install the component
         *
         * @return void
         */
        function install($parent)
        {
            $this->installOthers($parent);

            $db = JFactory::getDbo();

            /*
             * type_title 	varchar(255) 	Type title e.g. Article
                type_alias 	varchar(255) 	Type alias e.g. com_content.article
                table 	varchar(255) 	Information about the Table class
                rules 	text 	Not currently used
                field_mappings 	text 	Maps the table column names to standard Joomla! names
                router 	varchar(255) 	Optional: name of a router method
                content_history_options 	varchar(5120) 	Optional: ????
            */

            $tableExtensions = $db->quoteName("#__content_types");
            $type_title   = $db->quote("Affisione");
            $type_alias  = $db->quote("com_albopretorio.affissione");
            $table      = $db->quote('{"special": { "dbtable": "#__albopretorio", "key": "id", "type": "Albopretorio","prefix": "AlbopretorioTable","config": "array()"},"common": {"dbtable": "#__ucm_content", "key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()" }}');
            $field_mappings      = $db->quote('{"common":{"core_content_item_id":"id","core_title":"name","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"hits","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"featured", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"null", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"xreference", "asset_id":"null"}}');
            $router   = $db->quote("AlbopretorioHelperRoute::getAffissioneRoute");

            // Content type
            $db->setQuery(
                "INSERT INTO
                    $tableExtensions
                (
                    type_title,
                    type_alias,
                    `table`,
                    field_mappings,
                    router
                )
                VALUES(
                    $type_title,
                    $type_alias,
                    $table,
                    $field_mappings,
                    $router
                )
                "
            );
            $db->execute();


            // Category
            $type_title   = $db->quote("Albopretorio Category");
            $type_alias  = $db->quote("com_albopretorio.category");
            $table      = $db->quote('{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}');
            $field_mappings      = $db->quote('{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special": {"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}');
            $content_history_options = $db->quote('{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}');
            $router   = $db->quote("AlbopretorioHelperRoute::getCategoryRoute");
            $db->setQuery(
                "INSERT INTO
                    $tableExtensions
                (
                    type_title,
                    type_alias,
                    `table`,
                    field_mappings,
                    router,
                    `content_history_options`
                )
                VALUES(
                    $type_title,
                    $type_alias,
                    $table,
                    $field_mappings,
                    $router,
                    $content_history_options
                )
                "
            );
            $db->execute();

            $this->activatePlugins();

        }

        /**
         * method to install the component
         *
         * @return void
         */
        function update($parent)
        {

            $this->installOthers($parent);
            $this->activatePlugins();

        }

}
