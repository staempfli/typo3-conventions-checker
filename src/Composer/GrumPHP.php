<?php
namespace Staempfli\Typo3ConventionsChecker\Composer;

use Composer\Script\Event;

class GrumPHP {

    /**
     * Initializes grumphp for all TYPO3 extensions that have a grumphp.yml
     * configuration file in their root directory.
     * 
     * @param Event $event
     */
    public static function initializeExtensions(Event $event)
    {
        // Assume the project root directory to be the document root directory
        $documentRoot = $projectRootDirectory = getcwd();
        // Override document root directory
        $configuredDocumentRootSubdirectory = @$event->getComposer()->getPackage()->getExtra()['typo3/cms']['web-dir'];
        if ($configuredDocumentRootSubdirectory) {
            $documentRoot .= '/' . $configuredDocumentRootSubdirectory;
        }
        
        // Create absolute paths to both grumphp executable as well as to the
        // TYPO3 extension directory
        $grumphpExecutable = realpath($projectRootDirectory . '/vendor/bin/grumphp');
        $extensionDirectory = realpath($documentRoot . '/typo3conf/ext');
        
        if (empty($grumphpExecutable) || !file_exists($grumphpExecutable)) {
            $event->getIO()->writeError('GrumPHP doesn\'t seem to be installed!');
            return;
        }
        
        // Iterate through all extension directories
        foreach (scandir($extensionDirectory) as $extensionDirectoryName) {
            
            if (!preg_match('/[a-z_0-9]/i', $extensionDirectoryName)) {
                continue;
            }
            
            $extensionDirectoryAbsolute = $extensionDirectory . '/' . $extensionDirectoryName;
            $grumphpConfigFileAbsolute = realpath($extensionDirectoryAbsolute . '/grumphp.yml');
            
            // Extension directory doesn't have a grumphp.yml file - skip!
            if (!file_exists($grumphpConfigFileAbsolute)) {
                continue;
            }
            
            // Initialize extension
            $event->getIO()->write(sprintf('Initializing grumphp for extension "%s"...', $extensionDirectoryAbsolute));
            $initProcess = new \Symfony\Component\Process\Process(
                '"' . $grumphpExecutable . '" git:init',
                $extensionDirectoryAbsolute
            );
            $initProcess->run(function ($status, $content) {
                print $content;
            });
        }
    }
}