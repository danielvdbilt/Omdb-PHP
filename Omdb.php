<?php

/**
 * Omdb api
 *
 * @since   2017-11-13
 * @author  Daniel van de Bilt
 */
class Omdb
{
    /**
     * Can be short / full
     *
     * @var string
     */
    public $plot = 'short';

    /**
     * Can be movie, series, episode or NULL
     *
     * @var string|null
     */
    public $type = null;

    /**
     * Also get tomato scores
     *
     * @var bool
     */
    public $tomato = false;

    /**
     * Omdb api url
     *
     * @var string
     */
    private $_url = 'http://www.omdbapi.com/';

    /**
     * Omdb api image url
     *
     * @var string
     */
    private $_posterUrl = 'http://img.omdbapi.com/';

    /**
     * Api key
     *
     * @var null
     */
    private $_apiKey = null;

    /**
     * Max timeout
     *
     * @var int
     */
    private $_timeout = 5;

    /**
     * Add year to get better results
     *
     * @var null
     */
    private $_year = null;

    /**
     * Omdb constructor.
     *
     * @param   string $key
     */
    public function __construct($key)
    {
        $this->_apiKey = $key;
    }

    /**
     * Find movie by imdb ID
     *
     * @param   int $id
     * @return  mixed
     * @throws  \Exception
     */
    public function findByID($id)
    {
        // Checks if the IMDb id is valid
        if (self::isValidImdbID($id) === false) {
            throw new \Exception('The IMDb id is invalid.');
        }

        // Gets the URL
        $url = $this->_getUrl('i', $id);

        // Gets the data and returns it
        return $this->request($url);
    }

    /**
     * Find movie by title
     *
     * @param   string $title
     * @param   int    $year
     * @return  mixed
     */
    public function findByTitle($title, $year = null)
    {
        echo $title, PHP_EOL;

        // Set year
        $this->_year = $year;

        // Gets the URL
        $url = $this->_getUrl('t', $title);

        // Gets the data and returns it
        return $this->request($url);
    }

    /**
     * Find movie(s) by search key
     *
     * @param   string $key
     * @return  mixed
     */
    public function find($key)
    {
        // Gets the URL
        $url = $this->_getUrl('s', $key);

        // Gets the data and returns it
        return $this->request($url);
    }

    /**
     * Find post by imdb id
     *
     * @param   string $id
     * @param   int    $height
     * @return  mixed
     * @throws  \Exception
     */
    public function findPoster($id, $height = 1000)
    {
        // Checks if the IMDb id is valid
        if (self::isValidImdbID($id) === false) {
            throw new \Exception('The IMDb id is invalid.');
        }

        // Gets the URL
        $params = [
            'apiKey' => $this->_apiKey,
            'i'      => $id,
            'height' => $height
        ];

        $url = $this->_posterUrl . '?' . http_build_query($params);

        // Gets the data and returns it
        return $this->request($url, false);
    }

    /**
     * Build url
     *
     * @param   string $type
     * @param   string $value
     * @return  string
     */
    private function _getUrl($type, $value)
    {
        // All possible params
        $params = [
            'apiKey'   => $this->_apiKey,
            'y'        => $this->_year,
            'type'     => $this->type,
            'plot'     => $this->plot,
            'tomatoes' => $this->tomato,
            'r'        => 'json',
            'v'        => 1,
            $type      => $value
        ];

        // Prepare params
        foreach ($params as $param => $value) {

            // Bool to string
            if (is_bool($value)) {
                $params[$param] = ($value) ? 'true' : 'false';
            }

            // Ignore NULL values
            if (is_null($value) === true) {
                unset($params[$param]);
            }
        }

        $query = http_build_query($params);

        return $this->_url = $this->_url . '?' . $query;
    }

    /**
     * Do request
     *
     * @param   string $url
     * @param   bool   $responseIsJson
     * @return  mixed
     * @throws  \Exception
     */
    private function request($url, $responseIsJson = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $content  = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);

        // Check request status
        if ($curlInfo['http_code'] !== 200) {
            throw new \Exception('Request failed. HTTP CODE: ' . $curlInfo['http_code']);
        }

        return $responseIsJson ? json_decode($content) : $content;
    }

    /**
     * Check if string is valid imdb key
     *
     * @param   string $id
     * @return  bool
     */
    public static function isValidImdbID($id)
    {
        return (bool)preg_match('/^tt\d+?$/', $id);
    }
}
