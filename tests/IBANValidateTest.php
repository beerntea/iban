<?php

namespace Beerntea\IBAN\Test;

use Beerntea\IBAN;
use PHPUnit\Framework\TestCase;

class IBANValidateTest extends TestCase {
  const IBAN_TEST_NL = 'NL58ABNA0000000001';
  const IBAN_TEST_SANITIZE1 = 'NL 58 ABNA 0000 0000 01';
  const IBAN_TEST_SANITIZE2 = 'NL-58-ABNA-0000-0000-01';
  const IBAN_TEST_SANITIZE3 = '{NL}[58](ABNA)<0000>"0000"`01`';
  const IBAN_TEST_SANITIZE4 = '!@NL#$58%^ABNA&*0000;|0000,.01/:';

  public function testValidIbans(): void {
    $validIbans = require __DIR__ . '/data/ValidIbans.php';
    foreach ($validIbans as $iban) {
      $this->assertTrue((new IBAN($iban))->validate($error), "{$iban} => " . $error);
    }
  }

  public function testInvalidIbans(): void {
    $invalidIbans = require __DIR__ . '/data/InvalidIbans.php';
    foreach ($invalidIbans as $iban) {
      $this->assertFalse((new IBAN($iban))->validate($error), "{$iban} should not be valid");
    }
  }

  public function testIbanSanitation(): void {
    $ibans = [
      static::IBAN_TEST_SANITIZE1,
      static::IBAN_TEST_SANITIZE2,
      static::IBAN_TEST_SANITIZE3,
      static::IBAN_TEST_SANITIZE4
    ];
    foreach ($ibans as $iban) {
      $this->assertTrue((new IBAN($iban))->validate($error), "{$iban} => failed to sanitize");
    }
  }

  public function testGetCountryCode(): void {
    $this->assertEquals('NL', (new IBAN(static::IBAN_TEST_NL))->getCountryCode());
  }

  public function testGetChecksum(): void {
    $this->assertEquals('58', (new IBAN(static::IBAN_TEST_NL))->getChecksum());
  }

  public function testGetInstituteIdentification(): void {
    $this->assertEquals('ABNA', (new IBAN(static::IBAN_TEST_NL))->getInstituteIdentification());
  }

  public function testGetBankAccountNumber(): void {
    $this->assertEquals('0000000001', (new IBAN(static::IBAN_TEST_NL))->getBankAccountNumber());
  }

  public function testIbanFormatter(): void {
    $this->assertEquals('NL58 ABNA 0000 0000 01', (new IBAN(static::IBAN_TEST_NL))->format());
  }
}
