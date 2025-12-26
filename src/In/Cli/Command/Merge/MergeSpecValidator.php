<?php

namespace Gabriellopes\Pdfmanager\In\Cli\Command\Merge;

use RuntimeException;

class MergeSpecValidator
{
    /**
     * Validate and parse a merge spec file.
     *
     * @throws RuntimeException
     */
    public function validate(string $specPath): array
    {
        $this->assertFileExists($specPath);

        $data = $this->decodeJson($specPath);

        $this->assertRootStructure($data);
        $this->assertInputs($data['inputs']);

        return $this->normalize($data);
    }

    private function assertFileExists(string $path): void
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Spec file not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new RuntimeException("Spec file is not readable: {$path}");
        }
    }

    private function decodeJson(string $path): array
    {
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                'Invalid JSON: ' . json_last_error_msg()
            );
        }

        if (!is_array($data)) {
            throw new RuntimeException("Spec file must decode to an object");
        }

        return $data;
    }

    private function assertRootStructure(array $data): void
    {
        if (!isset($data['output']['file'])) {
            throw new RuntimeException("Missing output.file definition");
        }

        if (!is_string($data['output']['file']) || $data['output']['file'] === '') {
            throw new RuntimeException("output.file must be a non-empty string");
        }

        if (!isset($data['inputs']) || !is_array($data['inputs'])) {
            throw new RuntimeException("inputs must be a non-empty array");
        }

        if (count($data['inputs']) === 0) {
            throw new RuntimeException("At least one input PDF must be defined");
        }
    }

    private function assertInputs(array $inputs): void
    {
        foreach ($inputs as $index => $input) {
            if (!is_array($input)) {
                throw new RuntimeException("Input #{$index} must be an object");
            }

            if (!isset($input['file']) || !is_string($input['file']) || $input['file'] === '') {
                throw new RuntimeException("Input #{$index} missing valid file");
            }

            if (isset($input['include'])) {
                $this->assertInclude($input['include'], $index);
            }

            if (isset($input['exclude'])) {
                $this->assertPageList($input['exclude'], "exclude", $index);
            }
        }
    }

    private function assertInclude(mixed $include, int $index): void
    {
        if ($include === 'all') {
            return;
        }

        $this->assertPageList($include, "include", $index);
    }

    private function assertPageList(mixed $pages, string $field, int $index): void
    {
        if (!is_array($pages)) {
            throw new RuntimeException(
                "Input #{$index} {$field} must be an array of page numbers"
            );
        }

        foreach ($pages as $page) {
            if (!is_int($page) || $page < 1) {
                throw new RuntimeException(
                    "Input #{$index} {$field} contains invalid page number"
                );
            }
        }
    }

    /**
     * Normalize data so downstream code is predictable.
     */
    private function normalize(array $data): array
    {
        foreach ($data['inputs'] as &$input) {
            $input['include'] = $input['include'] ?? 'all';
            $input['exclude'] = $input['exclude'] ?? [];
        }

        return $data;
    }
}
