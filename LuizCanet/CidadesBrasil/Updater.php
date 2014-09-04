<?php

namespace LuizCanet\CidadesBrasil;

/**
 * Controls Json files updates.
 *
 * Setup states on creation. Loads data from IBGE website. And saves the result
 * in a configurable Json file.
 *
 * @property array $states Holds the states acronyms in lowercase
 *
 * @property array $cities Holds the cities data loaded by state
 *
 *
 */
class Updater
{

    public $states = array('ac', 'al', 'am', 'ap', 'ba', 'ce', 'df', 'es',
        'go', 'ma', 'mg', 'ms', 'mt', 'pa', 'pb', 'pe', 'pi', 'pr', 'rj', 'rn',
        'ro', 'rr', 'rs', 'sc', 'se', 'sp', 'to');

    public $cities = array();

    /**
     * Returns the list of states set.
     *
     * @return array
     */
    function getStates()
    {
        return $this->states;
    }

    /**
     * Object constructor. Can be setted with an array of states acronyms.
     * Defaults to array with all states defined by the Class.
     *
     * @param array $states
     */
    function __construct($states = array())
    {
        if (count($states) == 0) {
            $this->states = $this->getStates();
        } else {
            $this->states = $states;
        }
    }

    /**
     * Loads cities data from IBGE and save it to $cities property of object.
     */
    function loadCities()
    {
        foreach ($this->states as $state) {
            $html = file_get_contents('http://www.cidades.ibge.gov.br/download/mapa_e_municipios.php?uf='. $state);
            libxml_use_internal_errors(true);
            $this->cities[$state] = \DOMDocument::loadHTML($html);
            libxml_use_internal_errors(false);
        }
    }

    /**
     * Parses the HTML data loaded from each state set. Is possible to set an
     * other file by param.
     *
     * @param  string $file The path to file that will be written.
     */
    function saveToJson($file = 'cities-br.json')
    {
        $parsed_cities = array();
        foreach ($this->cities as $state => $cities) {
            $parsed_cities[$state] = array();
            $cities_table = $cities->getElementById('municipios');
            foreach ($cities_table->getElementsByTagName('tr') as $city) {
                if ($city->hasAttribute('class')) {
                    foreach ($city->getElementsByTagName('td') as $data) {
                        if ($data->getAttribute('class') == 'codigo') {
                            $cod = $data->nodeValue;
                        }
                        if ($data->getAttribute('class') == 'nome') {
                            $name = $data->nodeValue;
                        }
                    }
                    $parsed_cities[$state][$cod] = $name;
                }
            }
        }
        $json = json_encode($parsed_cities);
        file_put_contents($file, $json);
    }
}
