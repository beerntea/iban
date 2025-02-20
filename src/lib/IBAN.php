<?php

namespace Beerntea;

/**
 * IBAN information and validation library
 *
 * based on https://github.com/cmpayments/iban by Bas Peters <bp@cm.nl>
 * which was based on https://github.com/jschaedl/Iban by Jan Schaedlich <schaedlich.jan@gmail.com>
 *
 * @author Wouter van Groesen <wouter@beerntea.com>
 */

class IBAN {
  /** Semantic IBAN structure constants */
  const COUNTRY_CODE_OFFSET             = 0;
  const COUNTRY_CODE_LENGTH             = 2;
  const CHECKSUM_OFFSET                 = 2;
  const CHECKSUM_LENGTH                 = 2;
  const ACCOUNT_IDENTIFICATION_OFFSET   = 4;
  const INSTITUTE_IDENTIFICATION_OFFSET = 4;
  const INSTITUTE_IDENTIFICATION_LENGTH = 4;
  const BANK_ACCOUNT_NUMBER_OFFSET      = 8;
  const BANK_ACCOUNT_NUMBER_LENGTH      = 10;

  /**
   * Country code to size, regex format for each country that supports IBAN
   * @var array<string, array<int,int|string>>
   */
  public static array $ibanFormatMap = [
    'AA' => [12, '^[A-Z0-9]{12}$'],
    'AD' => [20, '^[0-9]{4}[0-9]{4}[A-Z0-9]{12}$'],
    'AE' => [19, '^[0-9]{3}[0-9]{16}$'],
    'AL' => [24, '^[0-9]{8}[A-Z0-9]{16}$'],
    'AO' => [21, '^[0-9]{21}$'],
    'AT' => [16, '^[0-9]{5}[0-9]{11}$'],
    'AX' => [14, '^[0-9]{6}[0-9]{7}[0-9]{1}$'],
    'AZ' => [24, '^[A-Z]{4}[A-Z0-9]{20}$'],
    'BA' => [16, '^[0-9]{3}[0-9]{3}[0-9]{8}[0-9]{2}$'],
    'BE' => [12, '^[0-9]{3}[0-9]{7}[0-9]{2}$'],
    'BF' => [23, '^[0-9]{23}$'],
    'BG' => [18, '^[A-Z]{4}[0-9]{4}[0-9]{2}[A-Z0-9]{8}$'],
    'BH' => [18, '^[A-Z]{4}[A-Z0-9]{14}$'],
    'BI' => [12, '^[0-9]{12}$'],
    'BJ' => [24, '^[A-Z]{1}[0-9]{23}$'],
    'BL' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'BR' => [25, '^[0-9]{8}[0-9]{5}[0-9]{10}[A-Z]{1}[A-Z0-9]{1}$'],
    'CH' => [17, '^[0-9]{5}[A-Z0-9]{12}$'],
    'CI' => [24, '^[A-Z]{1}[0-9]{23}$'],
    'CM' => [23, '^[0-9]{23}$'],
    'CR' => [17, '^[0-9]{4}[0-9]{13}$'],
    'CV' => [21, '^[0-9]{21}$'],
    'CY' => [24, '^[0-9]{3}[0-9]{5}[A-Z0-9]{16}$'],
    'CZ' => [20, '^[0-9]{4}[0-9]{6}[0-9]{10}$'],
    'DE' => [18, '^[0-9]{8}[0-9]{10}$'],
    'DK' => [14, '^[0-9]{4}[0-9]{9}[0-9]{1}$'],
    'DO' => [24, '^[A-Z0-9]{4}[0-9]{20}$'],
    'DZ' => [20, '^[0-9]{20}$'],
    'EE' => [16, '^[0-9]{2}[0-9]{2}[0-9]{11}[0-9]{1}$'],
    'ES' => [20, '^[0-9]{4}[0-9]{4}[0-9]{1}[0-9]{1}[0-9]{10}$'],
    'FI' => [14, '^[0-9]{6}[0-9]{7}[0-9]{1}$'],
    'FO' => [14, '^[0-9]{4}[0-9]{9}[0-9]{1}$'],
    'FR' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'GB' => [18, '^[A-Z]{4}[0-9]{6}[0-9]{8}$'],
    'GE' => [18, '^[A-Z]{2}[0-9]{16}$'],
    'GF' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'GI' => [19, '^[A-Z]{4}[A-Z0-9]{15}$'],
    'GL' => [14, '^[0-9]{4}[0-9]{9}[0-9]{1}$'],
    'GP' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'GR' => [23, '^[0-9]{3}[0-9]{4}[A-Z0-9]{16}$'],
    'GT' => [24, '^[A-Z0-9]{4}[A-Z0-9]{20}$'],
    'HR' => [17, '^[0-9]{7}[0-9]{10}$'],
    'HU' => [24, '^[0-9]{3}[0-9]{4}[0-9]{1}[0-9]{15}[0-9]{1}$'],
    'IE' => [18, '^[A-Z]{4}[0-9]{6}[0-9]{8}$'],
    'IL' => [19, '^[0-9]{3}[0-9]{3}[0-9]{13}$'],
    'IR' => [22, '^[0-9]{22}$'],
    'IS' => [22, '^[0-9]{4}[0-9]{2}[0-9]{6}[0-9]{10}$'],
    'IT' => [23, '^[A-Z]{1}[0-9]{5}[0-9]{5}[A-Z0-9]{12}$'],
    'JO' => [26, '^[A-Z]{4}[0-9]{4}[A-Z0-9]{18}$'],
    'KW' => [26, '^[A-Z]{4}[A-Z0-9]{22}$'],
    'KZ' => [16, '^[0-9]{3}[A-Z0-9]{13}$'],
    'LB' => [24, '^[0-9]{4}[A-Z0-9]{20}$'],
    'LC' => [28, '^[A-Z]{4}[A-Z0-9]{24}$'],
    'LI' => [17, '^[0-9]{5}[A-Z0-9]{12}$'],
    'LT' => [16, '^[0-9]{5}[0-9]{11}$'],
    'LU' => [16, '^[0-9]{3}[A-Z0-9]{13}$'],
    'LV' => [17, '^[A-Z]{4}[A-Z0-9]{13}$'],
    'MC' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'MD' => [20, '^[A-Z0-9]{2}[A-Z0-9]{18}$'],
    'ME' => [18, '^[0-9]{3}[0-9]{13}[0-9]{2}$'],
    'MF' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'MG' => [23, '^[0-9]{23}$'],
    'MK' => [15, '^[0-9]{3}[A-Z0-9]{10}[0-9]{2}$'],
    'ML' => [24, '^[A-Z]{1}[0-9]{23}$'],
    'MQ' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'MR' => [23, '^[0-9]{5}[0-9]{5}[0-9]{11}[0-9]{2}$'],
    'MT' => [27, '^[A-Z]{4}[0-9]{5}[A-Z0-9]{18}$'],
    'MU' => [26, '^[A-Z]{4}[0-9]{2}[0-9]{2}[0-9]{12}[0-9]{3}[A-Z]{3}$'],
    'MZ' => [21, '^[0-9]{21}$'],
    'NC' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'NL' => [14, '^[A-Z]{4}[0-9]{10}$'],
    'NO' => [11, '^[0-9]{4}[0-9]{6}[0-9]{1}$'],
    'PF' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'PK' => [20, '^[A-Z]{4}[A-Z0-9]{16}$'],
    'PL' => [24, '^[0-9]{8}[0-9]{16}$'],
    'PM' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'PS' => [25, '^[A-Z]{4}[A-Z0-9]{21}$'],
    'PT' => [21, '^[0-9]{4}[0-9]{4}[0-9]{11}[0-9]{2}$'],
    'QA' => [25, '^[A-Z]{4}[0-9]{4}[A-Z0-9]{17}$'],
    'RE' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'RO' => [20, '^[A-Z]{4}[A-Z0-9]{16}$'],
    'RS' => [18, '^[0-9]{3}[0-9]{13}[0-9]{2}$'],
    'SA' => [20, '^[0-9]{2}[A-Z0-9]{18}$'],
    'SC' => [27, '^[A-Z]{4}[0-9]{4}[0-9]{16}[A-Z]{3}$'],
    'SE' => [20, '^[0-9]{3}[0-9]{16}[0-9]{1}$'],
    'SI' => [15, '^[0-9]{5}[0-9]{8}[0-9]{2}$'],
    'SK' => [20, '^[0-9]{4}[0-9]{6}[0-9]{10}$'],
    'SM' => [23, '^[A-Z]{1}[0-9]{5}[0-9]{5}[A-Z0-9]{12}$'],
    'SN' => [24, '^[A-Z]{1}[0-9]{23}$'],
    'ST' => [21, '^[0-9]{8}[0-9]{11}[0-9]{2}$'],
    'TF' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'TL' => [19, '^[0-9]{3}[0-9]{14}[0-9]{2}$'],
    'TN' => [20, '^[0-9]{2}[0-9]{3}[0-9]{13}[0-9]{2}$'],
    'TR' => [22, '^[0-9]{5}[0-9]{1}[A-Z0-9]{16}$'],
    'UA' => [25, '^[0-9]{6}[A-Z0-9]{19}$'],
    'VG' => [20, '^[A-Z]{4}[0-9]{16}$'],
    'WF' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'],
    'XK' => [16, '^[0-9]{4}[0-9]{10}[0-9]{2}$'],
    'YT' => [23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$']
  ];

  /** Internal IBAN number */
  private string $iban = '';

  public function __construct(string $iban) {
    $this->iban = $this->normalize($iban);
  }

  /** Validates the supplied IBAN and provides passthrough failure message when validation fails */
  public function validate(?string &$error): bool {
    if (!$this->isCountryCodeValid()) {
      $error = 'IBAN country code not valid or not supported';
    } elseif (!$this->isLengthValid()) {
      $error = 'IBAN length is invalid';
    } elseif (!$this->isFormatValid()) {
      $error = 'IBAN format is invalid';
    } elseif (!$this->isChecksumValid()) {
      $error = 'IBAN checksum is invalid';
    } else {
      $error = null;
      return true;
    }

    return false;
  }

  /** Pretty print IBAN */
  public function format(): string {
    return sprintf(
      '%s %s %s',
      $this->getCountryCode() . $this->getChecksum(),
      substr($this->getInstituteIdentification(), 0, 4),
      implode(' ', str_split($this->getBankAccountNumber(), 4))
    );
  }

  /** Extract country code from IBAN */
  public function getCountryCode(): string {
    return substr($this->iban, static::COUNTRY_CODE_OFFSET, static::COUNTRY_CODE_LENGTH);
  }

  /** Extract checksum number from IBAN */
  public function getChecksum(): string {
    return substr($this->iban, static::CHECKSUM_OFFSET, static::CHECKSUM_LENGTH);
  }

  /** Extract Account Identification from IBAN */
  public function getAccountIdentification(): string {
    return substr($this->iban, static::ACCOUNT_IDENTIFICATION_OFFSET);
  }

  /** Extract Institute from IBAN */
  public function getInstituteIdentification(): string {
    return substr($this->iban, static::INSTITUTE_IDENTIFICATION_OFFSET, static::INSTITUTE_IDENTIFICATION_LENGTH);
  }

  /** Extract Bank Account number from IBAN */
  public function getBankAccountNumber(): string {
    $countryCode = $this->getCountryCode();
    $length = static::$ibanFormatMap[$countryCode][0] - static::INSTITUTE_IDENTIFICATION_LENGTH;
    return substr($this->iban, static::BANK_ACCOUNT_NUMBER_OFFSET, $length);
  }

  /** Validate IBAN length boundaries */
  private function isLengthValid(): bool {
    $countryCode = $this->getCountryCode();
    $validLength = static::COUNTRY_CODE_LENGTH + static::CHECKSUM_LENGTH + (int)static::$ibanFormatMap[$countryCode][0];

    return strlen($this->iban) === $validLength;
  }

  /** Validate IBAN country code */
  private function isCountryCodeValid(): bool {
    $countryCode = $this->getCountryCode();

    return !(isset(static::$ibanFormatMap[$countryCode]) === false);
  }

  /** Validate the IBAN format according to the country code */
  private function isFormatValid(): bool {
    $countryCode = $this->getCountryCode();
    $accountIdentification = $this->getAccountIdentification();

    return !(preg_match('/' . static::$ibanFormatMap[$countryCode][1] . '/', $accountIdentification) !== 1);
  }

  /** Validates if the checksum number is valid according to the IBAN */
  private function isChecksumValid(): bool {
    $countryCode = $this->getCountryCode();
    $checksum = $this->getChecksum();
    $accountIdentification = $this->getAccountIdentification();
    $numericCountryCode = $this->getNumericCountryCode($countryCode);
    $numericAccountIdentification = $this->getNumericAccountIdentification($accountIdentification);
    $invertedIban = $numericAccountIdentification . $numericCountryCode . $checksum;

    return bcmod($invertedIban, '97') === '1';
  }

  /** Extract country code from the IBAN as numeric code */
  private function getNumericCountryCode(string $countryCode): string {
    return $this->getNumericRepresentation($countryCode);
  }

  /** Extract account identification from the IBAN as numeric value */
  private function getNumericAccountIdentification(string $accountIdentification): string {
    return $this->getNumericRepresentation($accountIdentification);
  }

  /** Retrieve numeric presentation of a letter part of the IBAN */
  private function getNumericRepresentation(string $letterRepresentation): string {
    $numericRepresentation = '';

    foreach (str_split($letterRepresentation) as $char) {
      $ord = ord($char);
      if ($ord >= 65 && $ord <= 90) {
        $numericRepresentation .= (string) ($ord - 55);
      } elseif ($ord >= 48 && $ord <= 57) {
        $numericRepresentation .= (string) ($ord - 48);
      }
    }

    return $numericRepresentation;
  }

  /** Normalize IBAN by removing non-relevant characters and proper casing */
  private function normalize(string $iban): string {
    return (string)preg_replace('/[^a-z0-9]+/i', '', trim(strtoupper($iban)));
  }
}
