<?php

/**
 * MySweetAlert2 Plugin (MySweetAlert2/class.functions.php)
 * 
 * This class contains all the functions needed to operate.
 *
 * @package MySweetAlert2
 * @author Skryptec <skryptec@gmail.com>
 */ 

class MySweetAlert2_Functions {
    /**
     * Stores matched Javascript files.
     * @var array
     */
    private $jscripts;
    
    /**
     * Get all Javascript files in the jscripts directory.
     * 
     * @param string $type Just for backup references. 
     * 
     * @author Skryptec <skryptec@gmail.com>
     * @return boolean
     */
    public function getFiles(string $type) {
        $matched = [];

        foreach(glob('../jscripts/' . $type . '*.js') as $jscript) {
            (strpos(file_get_contents($jscript), '$.jGrowl(') !== false) ? array_push($matched, $jscript) : null;
        }

        if(count($matched) > 0) {
            $this->jscripts = $matched;

            return true;
        }

        return false;
    }

    /**
     * Replace all lines containing jGrowl with SweetAlert's code.
     * 
     * @param string $fileName The name of the file.
     * 
     * @author Skryptec <skryptec@gmail.com>
     */
    public function replaceFilesWithSwal(string $fileName) {
        file_put_contents($fileName, implode('', 
            array_map(function($data) {
                $match = stristr($data, '$.jGrowl'); 

                if($match) {
                    $filtered = explode(', ', trim(
                                    str_replace(
                                    [
                                        "{theme:'jgrowl_", 
                                        "'});", 
                                        '$.jGrowl('
                                    ], '', $match)
                                ));
                    
                    $title = ucfirst($filtered[1]) . '!';

                    $swal = "/** MySweetAlert2 */\nSwal.fire('$title', $filtered[0], '$filtered[1]');\n";

                    return $swal;
                }

                return $data;
            }, file($fileName))
        ));
    }

    /**
     * Creates a backup of all matched Javascript files.
     * 
     * @author Skryptec <skryptec@gmail.com>
     */
    public function createBackupForRevert() {
        mkdir('../jscripts/mysweetalert2_backup');

        foreach($this->jscripts as $jscript) {
            copy($jscript, substr_replace($jscript, 'mysweetalert2_backup/', 12, 0));

            $this->replaceFilesWithSwal($jscript);
        }
    }

    /**
     * Revert MySweetAlert2 plugin to original state.
     * 
     * @author Skryptec <skryptec@gmail.com>
     */
    public function revertSwal() {
        $this->getFiles('mysweetalert2_backup/');

        foreach($this->jscripts as $jscript) {
            copy($jscript, str_replace('mysweetalert2_backup/', '', $jscript));
            unlink($jscript);
        }

        rmdir('../jscripts/mysweetalert2_backup');
    }
}

$mySwalFunctions = new MySweetAlert2_Functions();
