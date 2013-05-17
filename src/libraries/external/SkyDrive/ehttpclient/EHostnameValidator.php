<?php

/**
 * Modified version of HostNameValidator of Zend for easy integration
 * with Yii as extension.
 *
 * Copyright (c) 2005-2010, Zend Technologies USA, Inc.
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 * 
 *     * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 *
 * @category   Yii
 * @package    EHttpClient
 */
class EHostnameValidator
{

    const IP_ADDRESS_NOT_ALLOWED  = 'hostnameIpAddressNotAllowed';
    const UNKNOWN_TLD             = 'hostnameUnknownTld';
    const INVALID_DASH            = 'hostnameDashCharacter';
    const INVALID_HOSTNAME_SCHEMA = 'hostnameInvalidHostnameSchema';
    const UNDECIPHERABLE_TLD      = 'hostnameUndecipherableTld';
    const INVALID_HOSTNAME        = 'hostnameInvalidHostname';
    const INVALID_LOCAL_NAME      = 'hostnameInvalidLocalName';
    const LOCAL_NAME_NOT_ALLOWED  = 'hostnameLocalNameNotAllowed';
	const NOT_IP_ADDRESS		  = 'notIpAddress';
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::IP_ADDRESS_NOT_ALLOWED  => "It appears to be an IP address, but IP addresses are not allowed",
        self::UNKNOWN_TLD             => "It appears to be a DNS hostname but cannot match TLD against known list",
        self::INVALID_DASH            => "Appears to be a DNS hostname but contains a dash (-) in an invalid position",
        self::INVALID_HOSTNAME_SCHEMA => "Appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'",
        self::UNDECIPHERABLE_TLD      => "Appears to be a DNS hostname but cannot extract TLD part",
        self::INVALID_HOSTNAME        => "It does not match the expected structure for a DNS hostname",
        self::INVALID_LOCAL_NAME      => "It does not appear to be a valid local network name",
        self::LOCAL_NAME_NOT_ALLOWED  => "It appears to be a local network name but local network names are not allowed",
        self::NOT_IP_ADDRESS 		  => "It does not appear to be a valid IP address"
    );


    /**
     * @var array
     */
    protected $_messageVariables = array(
        'tld' => '_tld'
    );

    /**
     * Allows Internet domain names (e.g., example.com)
     */
    const ALLOW_DNS   = 1;

    /**
     * Allows IP addresses
     */
    const ALLOW_IP    = 2;

    /**
     * Allows local network names (e.g., localhost, www.localdomain)
     */
    const ALLOW_LOCAL = 4;

    /**
     * Allows all types of hostnames
     */
    const ALLOW_ALL   = 7;

    /**
     * Whether IDN domains are validated
     *
     * @var boolean
     */
    private $_validateIdn = true;

    /**
     * Whether TLDs are validated against a known list
     *
     * @var boolean
     */
    private $_validateTld = true;

    /**
     * Bit field of ALLOW constants; determines which types of hostnames are allowed
     *
     * @var integer
     */
    protected $_allow;

    /**
     * Bit field of CHECK constants; determines what additional hostname checks to make
     *
     * @var unknown_type
     */
    // protected $_check;

    /**
     * Array of valid top-level-domains
     *
     * @var array
     * @see ftp://data.iana.org/TLD/tlds-alpha-by-domain.txt  List of all TLDs by domain
     */
    protected $_validTlds = array(
        'ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao',
        'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb',
        'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bm', 'bn', 'bo',
        'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cat', 'cc', 'cd',
        'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop',
        'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do',
        'dz', 'ec', 'edu', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj',
        'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh',
        'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu',
        'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il',
        'im', 'in', 'info', 'int', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm',
        'jo', 'jobs', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw',
        'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu',
        'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm',
        'mn', 'mo', 'mobi', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv',
        'mw', 'mx', 'my', 'mz', 'na', 'name', 'nc', 'ne', 'net', 'nf', 'ng',
        'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'org', 'pa', 'pe',
        'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt',
        'pw', 'py', 'qa', 're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd',
        'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr',
        'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj',
        'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel', 'tt', 'tv', 'tw',
        'tz', 'ua', 'ug', 'uk', 'um', 'us', 'uy', 'uz', 'va', 'vc', 've',
        'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm',
        'zw'
        );
    protected $_IdnChars = array(
    	'at'=>'\x{00EO}-\x{00F6}\x{00F8}-\x{00FF}\x{0153}\x{0161}\x{017E}',
    	'ch'=>'\x{00EO}-\x{00F6}\x{00F8}-\x{00FF}\x{0153}',
    	'de'=>array('\x{00E1}\x{00E0}\x{0103}\x{00E2}\x{00E5}\x{00E4}\x{00E3}\x{0105}\x{0101}\x{00E6}\x{0107}',
              '\x{0109}\x{010D}\x{010B}\x{00E7}\x{010F}\x{0111}\x{00E9}\x{00E8}\x{0115}\x{00EA}\x{011B}' ,
              '\x{00EB}\x{0117}\x{0119}\x{0113}\x{011F}\x{011D}\x{0121}\x{0123}\x{0125}\x{0127}\x{00ED}' ,
              '\x{00EC}\x{012D}\x{00EE}\x{00EF}\x{0129}\x{012F}\x{012B}\x{0131}\x{0135}\x{0137}\x{013A}' ,
              '\x{013E}\x{013C}\x{0142}\x{0144}\x{0148}\x{00F1}\x{0146}\x{014B}\x{00F3}\x{00F2}\x{014F}' ,
              '\x{00F4}\x{00F6}\x{0151}\x{00F5}\x{00F8}\x{014D}\x{0153}\x{0138}\x{0155}\x{0159}\x{0157}' ,
              '\x{015B}\x{015D}\x{0161}\x{015F}\x{0165}\x{0163}\x{0167}\x{00FA}\x{00F9}\x{016D}\x{00FB}' ,
              '\x{016F}\x{00FC}\x{0171}\x{0169}\x{0173}\x{016B}\x{0175}\x{00FD}\x{0177}\x{00FF}\x{017A}' ,
              '\x{017E}\x{017C}\x{00F0}\x{00FE}'),
    	'fi'=>'\x{00E5}\x{00E4}\x{00F6}',
    	'hu'=>'\x{00E1}\x{00E9}\x{00ED}\x{00F3}\x{00F6}\x{0151}\x{00FA}\x{00FC}\x{0171}',
    	'li'=>'\x{00EO}-\x{00F6}\x{00F8}-\x{00FF}\x{0153}',
    	'no'=>array('\x00E1\x00E0\x00E4\x010D\x00E7\x0111\x00E9\x00E8\x00EA\x\x014B' ,
                '\x0144\x00F1\x00F3\x00F2\x00F4\x00F6\x0161\x0167\x00FC\x017E\x00E6' ,
                '\x00F8\x00E5'),
    	'se'=> '\x{00E5}\x{00E4}\x{00F6}\x{00FC}\x{00E9}'
    );
    protected $_errors = array();

    /**
     * @var string
     */
    protected $_tld;

    /**
     * Sets validator options
     *
     * @param integer          $allow       OPTIONAL Set what types of hostname to allow (default ALLOW_DNS)
     * @param boolean          $validateIdn OPTIONAL Set whether IDN domains are validated (default true)
     * @param boolean          $validateTld OPTIONAL Set whether the TLD element of a hostname is validated (default true)
     * @return void
     * @see http://www.iana.org/cctld/specifications-policies-cctlds-01apr02.htm  Technical Specifications for ccTLDs
     */
    public function __construct($allow = self::ALLOW_DNS, $validateIdn = true, $validateTld = true)
    {
        // Set allow options
        $this->setAllow($allow);

        // Set validation options
        $this->_validateIdn = $validateIdn;
        $this->_validateTld = $validateTld;

    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid IP address
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValidIp($value)
    {
        $valueString = (string) $value;

       // $this->_setValue($valueString);

        if (ip2long($valueString) === false) {
            //$this->_error();
            return false;
        }

        return true;
    }

	public function getErrors()
    {
        return $this->_errors;
    }
    /**
     * Returns the allow option
     *
     * @return integer
     */
    public function getAllow()
    {
        return $this->_allow;
    }

    /**
     * Sets the allow option
     *
     * @param  integer $allow
     * @return EValidateHostname Provides a fluent interface
     */
    public function setAllow($allow)
    {
        $this->_allow = $allow;
        return $this;
    }

    /**
     * Set whether IDN domains are validated
     *
     * This only applies when DNS hostnames are validated
     *
     * @param boolean $allowed Set allowed to true to validate IDNs, and false to not validate them
     */
    public function setValidateIdn ($allowed)
    {
        $this->_validateIdn = (bool) $allowed;
    }

    /**
     * Set whether the TLD element of a hostname is validated
     *
     * This only applies when DNS hostnames are validated
     *
     * @param boolean $allowed Set allowed to true to validate TLDs, and false to not validate them
     */
    public function setValidateTld ($allowed)
    {
        $this->_validateTld = (bool) $allowed;
    }


    
    /**
     * Returns true if and only if the $value is a valid hostname with respect to the current allow option
     *
     * @param  string $value
     * @throws CException if a fatal error occurs for validation process
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

       // $this->_setValue($valueString);

        // Check input against IP address schema
        if ($this->isValidIp($valueString)) {
            if (!($this->_allow & self::ALLOW_IP)) {
                $this->_errors[self::IP_ADDRESS_NOT_ALLOWED] = $this->_messageTemplates[self::IP_ADDRESS_NOT_ALLOWED];
                return false;
            } else{
                return true;
            }
        }

        // Check input against DNS hostname schema
        $domainParts = explode('.', $valueString);
        if ((count($domainParts) > 1) && (strlen($valueString) >= 4) && (strlen($valueString) <= 254)) {
            $status = false;

            do {
                // First check TLD
                if (preg_match('/([a-z]{2,10})$/i', end($domainParts), $matches)) {

                    reset($domainParts);

                    // Hostname characters are: *(label dot)(label dot label); max 254 chars
                    // label: id-prefix [*ldh{61} id-prefix]; max 63 chars
                    // id-prefix: alpha / digit
                    // ldh: alpha / digit / dash

                    // Match TLD against known list
                    $this->_tld = strtolower($matches[1]);
                    if ($this->_validateTld) {
                        if (!in_array($this->_tld, $this->_validTlds)) {
                            $this->_error[self::UNKNOWN_TLD] = $this->_messageTemplates[self::UNKNOWN_TLD];
                            $status = false;
                            break;
                        }
                    }

                    /**
                     * Match against IDN hostnames
                     * @see EValidateHostnameInterface
                     */
                    $labelChars = 'a-z0-9';
                    $utf8 = false;
                    
                    if ($this->_validateIdn) {
                         // Load additional characters
                         if(array_key_exists($this->_tld,$this->_IdnChars)){
                         	$labelChars .= is_scalar($this->_IdnChars[$this->_tld])? $this->_IdnChars[$this->_tld]:join('',$this->_IdnChars[$this->_tld]);
                            $utf8 = true;
                        }
                    }

                    // Keep label regex short to avoid issues with long patterns when matching IDN hostnames
                    $regexLabel = '/^[' . $labelChars . '\x2d]{1,63}$/i';
                    if ($utf8) {
                        $regexLabel .= 'u';
                    }

                    // Check each hostname part
                    $valid = true;
                    foreach ($domainParts as $domainPart) {

                        // Check dash (-) does not start, end or appear in 3rd and 4th positions
                        if (strpos($domainPart, '-') === 0 ||
                        (strlen($domainPart) > 2 && strpos($domainPart, '-', 2) == 2 && strpos($domainPart, '-', 3) == 3) ||
                        strrpos($domainPart, '-') === strlen($domainPart) - 1) {

                            $this->_errors[self::INVALID_DASH] = $this->_messageTemplates[self::INVALID_DASH];
                            $status = false;
                            break 2;
                        }

                        // Check each domain part
                        $status = @preg_match($regexLabel, $domainPart);
                        if ($status === false) {
                            /**
                             * Regex error
                             * @see CException
                             */
                           
                            throw new CException('Internal error: DNS validation failed');
                        } elseif ($status === 0) {
                            $valid = false;
                        }
                    }

                    // If all labels didn't match, the hostname is invalid
                    if (!$valid) {
                        $this->_errors[self::INVALID_HOSTNAME_SCHEMA] = $this->_messageTemplates[self::INVALID_HOSTNAME_SCHEMA];
                        $status = false;
                    }

                } else {
                    // Hostname not long enough
                    $this->_errors[self::UNDECIPHERABLE_TLD] = $this->_messageTemplates[self::UNDECIPHERABLE_TLD];
                    $status = false;
                }
            } while (false);

            // If the input passes as an Internet domain name, and domain names are allowed, then the hostname
            // passes validation
            if ($status && ($this->_allow & self::ALLOW_DNS)) {
                return true;
            }
        } else {
            $this->_errors[self::INVALID_HOSTNAME] = $this->_messageTemplates[self::INVALID_HOSTNAME];
        }

        // Check input against local network name schema; last chance to pass validation
        $regexLocal = '/^(([a-zA-Z0-9\x2d]{1,63}\x2e)*[a-zA-Z0-9\x2d]{1,63}){1,254}$/';
        $status = @preg_match($regexLocal, $valueString);
        if (false === $status) {
            /**
             * Regex error
             * @see CException
             */
        
            throw new CException('Internal error: local network name validation failed');
        }

        // If the input passes as a local network name, and local network names are allowed, then the
        // hostname passes validation
        $allowLocal = $this->_allow & self::ALLOW_LOCAL;
        if ($status && $allowLocal) {
            return true;
        }

        // If the input does not pass as a local network name, add a message
        if (!$status) {
            $this->_errors[self::INVALID_LOCAL_NAME] = $this->_messageTemplates[self::INVALID_LOCAL_NAME];
        }

        // If local network names are not allowed, add a message
        if ($status && !$allowLocal) {
            $this->_errors[self::LOCAL_NAME_NOT_ALLOWED] = $this->_messageTemplates[self::LOCAL_NAME_NOT_ALLOWED];
        }

        return false;
    }

}
