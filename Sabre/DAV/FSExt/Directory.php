<?php

    require_once 'Sabre/DAV/FSExt/Node.php';
    require_once 'Sabre/DAV/IDirectory.php';
    require_once 'Sabre/DAV/FSExt/File.php';

    /**
     * Directory class 
     * 
     * @package Sabre
     * @subpackage DAV
     * @version $Id$
     * @copyright Copyright (C) 2007, 2008 Rooftop Solutions. All rights reserved.
     * @author Evert Pot (http://www.rooftopsolutions.nl/) 
     * @license licence http://www.freebsd.org/copyright/license.html  BSD License (4 Clause)
     */
    class Sabre_DAV_FSExt_Directory extends Sabre_DAV_FSExt_Node implements Sabre_DAV_IDirectory {

        /**
         * Creates a new file in the directory 
         * 
         * @param string $name Name of the file 
         * @param string $data Initial payload 
         * @return void
         */
        public function createFile($name, $data = null) {

            $newPath = $this->path . '/' . $name;
            file_put_contents($newPath,$data);

        }

        /**
         * Creates a new subdirectory 
         * 
         * @param string $name 
         * @return void
         */
        public function createDirectory($name) {

            $newPath = $this->path . '/' . $name;
            mkdir($newPath);

        }

        /**
         * Returns a specific child node, referenced by its name 
         * 
         * @param string $name 
         * @throws Sabre_DAV_FileNotFoundException
         * @return Sabre_DAV_INode 
         */
        public function getChild($name) {


            $path = $this->path . '/' . $name;

            if (!file_exists($path)) throw new Sabre_DAV_FileNotFoundException('File could not be located');
            if (strpos($name,'.')===0) throw new Sabre_DAV_PermissionDeniedException('Permission denied to access this file'); 

            if (is_dir($path)) {

                return new Sabre_DAV_FSExt_Directory($path);

            } else {

                return new Sabre_DAV_FSExt_File($path);

            }

        }

        /**
         * Returns an array with all the child nodes 
         * 
         * @return Sabre_DAV_INode[] 
         */
        public function getChildren() {

            $nodes = array();
            foreach(scandir($this->path) as $node) if($node[0]!='.') $nodes[] = $this->getChild($node);
            return $nodes;

        }

        /**
         * Deletes all files in this directory, and then itself 
         * 
         * @return void
         */
        public function delete() {

            foreach($this->getChildren() as $child) $child->delete();
            rmdir($this->path);

        }

    }

?>