<?php

namespace Wolff\Core\Http;

class File
{

    /**
     * Data of the file
     * @var array
     */
    private $data;

    /**
     * List of options for uploading files
     * @var array
     */
    private $options;


    /**
     * Default constructor
     *
     * @param array $data the file data
     * @param array $options the array with the file options
     */
    public function __construct(array $data, array &$options)
    {
        $this->data = $data;
        $this->options = &$options;
    }


    /**
     * Returns the specified file value
     *
     * @param string $key the value key
     *
     * @return mixed the specified file value
     */
    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }


    /**
     * Uploads the file with the given path.
     * If no path is provided, the file name will be
     * used instead
     *
     * @param string|null $name the desired file path
     *
     * @return bool True if the file has been uploaded,
     * false otherwise
     */
    public function upload(string $name = null)
    {
        $path = $this->options['dir'] . '/' . ($name ?? $this->data['name']);

        if (!$this->complies($path)) {
            return false;
        }

        return move_uploaded_file($this->data['tmp_name'], $path);
    }


    /**
     * Returns true if the current file complies
     * with the current options, false otherwise
     *
     * @param string $path the path of the file
     *
     * @return bool True if the current file complies
     * with the current options, false otherwise
     */
    private function complies(string $path)
    {
        $extension = pathinfo($this->data['name'], PATHINFO_EXTENSION);

        if (!empty($this->options['extensions']) &&
            !in_array($extension, $this->options['extensions'])) {
            return false;
        }

        if ($this->options['max_size'] > 0 &&
            $this->options['max_size'] < $this->data['size']) {
            return false;
        }

        if (!$this->options['override'] && file_exists($path)) {
            return false;
        }

        return true;
    }
}
