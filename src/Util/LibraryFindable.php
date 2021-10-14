<?php

declare(strict_types=1);

namespace NFC\Util;

use NFC\NFCException;

trait LibraryFindable
{
    protected ?array $libraryPaths = null;

    protected function setLibraryPaths($libraryPaths = null): self
    {
        if ($libraryPaths === null) {
            $this->libraryPaths = $this->isWindows()
                // Windows does not supported for auto scan locations.
                ? []
                : (
                    property_exists($this, 'autoScanLocationsForUnix')
                        ? $this->autoScanLocationsForUnix
                        : []
                );

            if (!$this->isWindows() && property_exists($this, 'defaultLibraryPath') && is_string($this->defaultLibraryPath)) {
                // Add working directory
                $this->libraryPaths[] = getcwd() . '/' . $this->defaultLibraryPath;
            }
        } else {
            $this->libraryPaths = (array) $libraryPaths;
        }

        return $this;
    }

    public function createCDefHeader(): string
    {
        return implode(
            "\n",
            array_map(
                function (array $header) {
                    $headerFile = $header[0] ?? null;
                    $bindDefines = $header[1] ?? null;

                    if (!is_file($headerFile)) {
                        throw new NFCException("Cannot open a header file: {$headerFile}");
                    }

                    $load = file_get_contents($headerFile);
                    if ($bindDefines !== null) {
                        foreach ($bindDefines as $bindListedDefinesClassName) {
                            $defines = $bindListedDefinesClassName::all();
                            $load = str_replace(
                                array_keys($defines),
                                array_values($defines),
                                $load
                            );
                        }
                    }

                    return $load;
                },
                property_exists($this, 'headers')
                    ? $this->headers
                    : []
            )
        );
    }

    public function selectLibraryPath(): string
    {
        $this->getLogger()->info(
            'Search using libnfc library'
        );

        $libraryPath = null;
        foreach ($this->libraryPaths as $path) {
            if (is_file($path)) {
                $libraryPath = $path;
                $this->getLogger()->info(
                    "Use library: {$libraryPath}"
                );
                break;
            }
            if (is_dir($path)) {
                foreach ((property_exists($this, 'autoScanLibraryNames') ? $this->autoScanLibraryNames : []) as $name) {
                    if (is_file($foundFilePath = $path . DIRECTORY_SEPARATOR . $name)) {
                        $libraryPath = $foundFilePath;

                        $this->getLogger()->info(
                            "Use library: {$libraryPath}"
                        );
                        break 2;
                    }
                }
            }
        }

        if ($libraryPath === null) {
            throw new NFCException('Library not found.');
        }

        return $libraryPath;
    }

    protected function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }
}
