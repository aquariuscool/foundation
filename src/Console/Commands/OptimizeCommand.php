<?php

namespace Orchestra\Foundation\Console\Commands;

use RuntimeException;
use ClassPreloader\Factory;
use ClassPreloader\Exceptions\SkipFileException;
use ClassPreloader\Exceptions\VisitorExceptionInterface;
use Illuminate\Foundation\Console\OptimizeCommand as Command;

class OptimizeCommand extends Command
{
    /**
     * Generate the compiled class file.
     *
     * @return void
     */
    protected function compileClasses()
    {
        $preloader = (new Factory())->create(['skip' => true]);

        $path = $this->laravel->getCachedCompilePath();

        if (file_exists($path)) {
            unlink($path);
        }

        $handle = $preloader->prepareOutput($path.'.tmp');

        foreach ($this->getClassFiles() as $file) {
            try {
                fwrite($handle, $preloader->getCode($file, false)."\n");
            } catch (SkipFileException $ex) {
                // Class Preloader 2.x
            } catch (VisitorExceptionInterface $e) {
                // Class Preloader 3.x
            } catch (RuntimeException $e) {
                // Handle when fwrite fails.
            }
        }

        fclose($handle);

        rename($path.'.tmp', $path);
    }
}
