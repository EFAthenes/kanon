<?php
/*
* (c) Andreas Fischer <af@bantuX.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
* Wrapper class around built-in ini_get() function.
*
* Provides easier handling of the different interpretations of ini values.
*/
/*
 * File modified for the KProject Framework
 * 
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 */
class IniGetWrapper
{
    /**
    * Simple wrapper around ini_get()
    * See http://php.net/manual/en/function.ini-get.php
    *
    * @param mixed $varname  The configuration option name.
    * @return null|string     Null if configuration option does not exist.
    *                         The configuration option value (string) otherwise.
    */
    public function get(mixed $varname) : ?string
    {
        $var="".$varname;
        $value = $this->getPhp($var);
        return $value === false ? null : $value;
    }

    /**
    * Gets the configuration option value as a trimmed string.
    *
    * @param mixed $varname  The configuration option name.
    * @return null|string     Null if configuration option does not exist.
    *                         The configuration option value (string) otherwise.
    */
    public function getString(mixed $varname) : ?string
    {
        $value = $this->get($varname);
        return $value === null ? null : trim($value);
    }

    /**
    * Gets configuration option value as a boolean.
    * Interprets the string value 'off' as false.
    *
    * @param mixed $varname  The configuration option name.
    * @return null|bool       Null if configuration option does not exist.
    *                         False if configuration option is disabled.
    *                         True otherwise.
    */
    public function getBool(mixed $varname) : ? bool
    {
        $value = $this->getString($varname);
        return $value === null ? null : $value && strtolower($value) !== 'off';
    }

    /**
    * Gets configuration option value as an integer.
    *
    * @param mixed $varname  The configuration option name.
    * @return null|float  Null if configuration option does not exist or is not numeric.
    *                         The configuration option value (integer or float) otherwise.
    */
    public function getNumeric(mixed $varname) : int|float|null
    {
        $value = $this->getString($varname);
        return is_numeric($value) ? $value + 0 : null;
    }

    /**
    * Gets configuration option value in bytes.
    * Converts strings like '128M' to bytes (integer or float).
    *
    * @param string $varname  The configuration option name.
    * @return null|int|float  Null if configuration option does not exist or is not well-formed.
    *                         The configuration option value as bytes (integer or float) otherwise.
    */
    public function getBytes(mixed $varname)
    {
        $value = $this->getString($varname);

        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            // Already in bytes.
            return $value + 0;
        }

        return $this->convertBytes("".$value);
    }

    /**
    * Gets configuration option value as a list (array).
    * Converts comma-separated string into list (array).
    *
    * @param string $varname  The configuration option name.
    * @return null|array<int,string>      Null if configuration option does not exist.
    *                         The configuration option value as a list (array) otherwise.
    */
    public function getList(mixed $varname) : ?array
    {
        $value = $this->getString($varname);
        return $value === null ? null : array_map('trim', explode(',', $value));
    }

    /**
    * Checks whether a list contains a given element (string).
    *
    * @param string $varname  The configuration option name.
    * @param string $needle   The element to check whether it is contained in the list.
    * @return null|bool       Null if configuration option does not exist.
    *                         Whether $needle is contained in the list otherwise.
    */
    public function listContains(mixed $varname, mixed $needle)
    {
        $list = $this->getList($varname);
        return $list === null ? null : in_array("".$needle, $list, true);
    }

    /**
    * @param string $varname  The configuration option name.
    */
    protected function getPhp(string $varname) : string|false
    {
        return ini_get($varname);
    }

    /**
    * @param string $string   String containing a numeric and ending with a unit, e.g. 128M.
    * @return null|int|float
    */
    protected function convertBytes(string $string) : int|float|null
    {
        $numeric = substr($string, 0, -1);
        if (!is_numeric($numeric)) {
            return null;
        }

        switch (strtolower($string[strlen($string) - 1])) {
            case 'g':
                $numeric *= 1024;
                // no break
            case 'm':
                $numeric *= 1024;
                // no break
            case 'k':
                $numeric *= 1024;
                break;

            default:
                // Does not carry a valid unit.
                return null;
        }

        return $numeric;
    }
}
